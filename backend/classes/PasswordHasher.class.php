<?php
//declare(strict_types = 1);

//namespace Microsoft\AspNetCore\Identity;

//use RuntimeException;

abstract class KeyDerivationPrf
{
	const HMACSHA1 = 0;
	const HMACSHA256 = 1;
	const HMACSHA512 = 2;
}

abstract class PasswordHasherCompatibilityMode
{
	const IdentityV2 = 0;
	const IdentityV3 = 1;
}

/*
 * Class PasswordHasher
 * @package Microsoft\AspNetCore\Identity
 * A direct port of the algorithm to generate and verify ASP.NET password hashes found here:
 * @link https://aspnetidentity.codeplex.com/SourceControl/latest#src/Microsoft.AspNet.Identity.Core/Crypto.cs
 * For mor information:
 * @link http://stackoverflow.com/questions/20621950/asp-net-identity-default-password-hasher-how-does-it-work-and-is-it-secure
 */
class PasswordHasher
{
	private static $_compatibilityMode = PasswordHasherCompatibilityMode::IdentityV3;
	private static $_iterCount = 10000;
	
	/*
	params
		string $password
		
	return value
		string
	*/
	public static function HashPassword($password)
	{
		if (is_null($password)) {
			throw new RuntimeException("$password cannot be null.");
		}
		
		if (PasswordHasher::$_compatibilityMode == PasswordHasherCompatibilityMode::IdentityV2) {
			return base64_encode(pack("C*", ...PasswordHasher::HashPasswordV2($password)));
		} else {
			$hashArray = PasswordHasher::HashPasswordV3(
				$password,
				KeyDerivationPrf::HMACSHA256,
				PasswordHasher::$_iterCount,
				128 / 8,
				256 / 8);
			return base64_encode(pack("C*", ...$hashArray));
		}
	}
	
	/*
	params
		string $password
		
	return value
		byte[]
	*/
	private static function HashPasswordV2($password)
	{
		$pbkdf2Prf = KeyDerivationPrf::HMACSHA1; // default for Rfc2898DeriveBytes
		$pbkdf2IterCount = 1000; // default for Rfc2898DeriveBytes
		$pbkdf2SubkeyLength = 256 / 8; // 256 bits
		$saltSize = 128 / 8; // 128 bits
		
		// Produce a version 2 (see comment above) text hash.
		$salt = array_fill(0, $saltSize, 0);
		for ($i = 0; $i < count($salt); $i++) {
			$salt[$i] = mt_rand(0, 255);
		}
		$subkey = unpack("C*", hash_pbkdf2(PasswordHasher::GetHashAlgoFromPrf($pbkdf2Prf), $password, pack("C*", ...$salt), $pbkdf2IterCount, $pbkdf2SubkeyLength, true));
		// since unpack will create an array starting with index 1, we need to unshift and shift the array to make its index starts at 0
		array_unshift($subkey, 0);
		array_shift($subkey);
		
		$outputBytes = array_fill(0, 1 + $saltSize + $pbkdf2SubkeyLength, 0);
		$outputBytes[0] = 0; // format marker
		PasswordHasher::BlockCopy($salt, 0, $outputBytes, 1, $saltSize);
		PasswordHasher::BlockCopy($subkey, 0, $outputBytes, 1 + $saltSize, $pbkdf2SubkeyLength);
		return $outputBytes;
	}
	
	/*
	params
		string $password
		int $prf
		int $iterCount
		int $saltSize
		int $numBytesRequested
		
	return value
		byte[]
	*/
	private static function HashPasswordV3($password, $prf, $iterCount, $saltSize, $numBytesRequested)
	{
		// Produce a version 3 (see comment above) text hash.
		$salt = array_fill(0, $saltSize, 0);
		for ($i = 0; $i < count($salt); $i++) {
			$salt[$i] = mt_rand(0, 255);
		}
		$subkey = unpack("C*", hash_pbkdf2(PasswordHasher::GetHashAlgoFromPrf($prf), $password, pack("C*", ...$salt), $iterCount, $numBytesRequested, true));
		// since unpack will create an array starting with index 1, we need to unshift and shift the array to make its index starts at 0
		array_unshift($subkey, 0);
		array_shift($subkey);
		
		$outputBytes = array_fill(0, 13 + count($salt) + count($subkey), 0);
		$outputBytes[0] = 1; // format marker
		PasswordHasher::WriteNetworkByteOrder($outputBytes, 1, $prf);
		PasswordHasher::WriteNetworkByteOrder($outputBytes, 5, $iterCount);
		PasswordHasher::WriteNetworkByteOrder($outputBytes, 9, $saltSize);
		PasswordHasher::BlockCopy($salt, 0, $outputBytes, 13, count($salt));
		PasswordHasher::BlockCopy($subkey, 0, $outputBytes, 13 + $saltSize, count($subkey));
		return $outputBytes;
	}
	
	/*
	params
		string $hashedPassword
		string $providedPassword
		
	return value
		bool
	*/
	public static function VerifyHashedPassword($hashedPassword, $providedPassword)
	{
		if (is_null($hashedPassword)) {
			throw new RuntimeException("$hashedPassword cannot be null.");
		}
		if (is_null($providedPassword)) {
			throw new RuntimeException("$providedPassword cannot be null.");
		}

		$decodedHashedPassword = base64_decode($hashedPassword);
		$decodedHashedPassword = unpack("C*", $decodedHashedPassword);	// convert binary to byte array
		// since unpack will create an array starting with index 1, we need to unshift and shift the array to make its index starts at 0
		array_unshift($decodedHashedPassword, 0);
		array_shift($decodedHashedPassword);
		// read the format marker from the hashed password
		if (count($decodedHashedPassword) == 0)
		{
			return false;
		}
		
		switch ($decodedHashedPassword[0]) {
			case 0:
				if (PasswordHasher::VerifyHashedPasswordV2($decodedHashedPassword, $providedPassword)) {
					// This is an old password hash format - the caller needs to rehash if we're not running in an older compat mode.
					return (PasswordHasher::$_compatibilityMode == PasswordHasherCompatibilityMode::IdentityV3)
						? true //PasswordVerificationResult.SuccessRehashNeeded
						: true; //PasswordVerificationResult.Success;
				} else {
					return false;
				}
				
			case 1:
				$embeddedIterCount = 0;
				if (PasswordHasher::VerifyHashedPasswordV3($decodedHashedPassword, $providedPassword, $embeddedIterCount)) {
					// If this hasher was configured with a higher iteration count, change the entry now.
                        return $embeddedIterCount < PasswordHasher::$_iterCount
                            ? true //PasswordVerificationResult.SuccessRehashNeeded
                            : true; //PasswordVerificationResult.Success;
				} else {
					return false;
				}
			
			default:
				return false;
		}
	}
	
	/*
	params
		byte[] $hashedPassword
		string $password
		
	return value
		bool
	*/
	private static function VerifyHashedPasswordV2($hashedPassword, $password)
	{
		$pbkdf2Prf = KeyDerivationPrf::HMACSHA1; // default for Rfc2898DeriveBytes
		$pbkdf2IterCount = 1000; // default for Rfc2898DeriveBytes
		$pbkdf2SubkeyLength = 256 / 8; // 256 bits
		$saltSize = 128 / 8; // 128 bits
		
		// We know ahead of time the exact length of a valid hashed password payload.
		if (count(hashedPassword) != 1 + $saltSize + $pbkdf2SubkeyLength) {
			return false; // bad size
		}
		
		$salt = array_fill(0, $saltSize, 0);
		PasswordHasher::BlockCopy($hashedPassword, 1, $salt, 0, count($salt));
		
		$expectedSubkey = array_fill(0, $pbkdf2SubkeyLength, 0);
		PasswordHasher::BlockCopy($hashedPassword, 1 + count($salt), $expectedSubkey, 0, count($expectedSubkey));
		
		// Hash the incoming password and verify it
		$actualSubkey = unpack("C*", hash_pbkdf2(PasswordHasher::GetHashAlgoFromPrf($pbkdf2Prf), $password, pack("C*", ...$salt), $pbkdf2IterCount, $pbkdf2SubkeyLength, true));
		// since unpack will create an array starting with index 1, we need to unshift and shift the array to make its index starts at 0
		array_unshift($actualSubkey, 0);
		array_shift($actualSubkey);
		return PasswordHasher::ByteArraysEqual($actualSubkey, $expectedSubkey);
	}
	
	/*
	params
		byte[] $hashedPassword
		string $password
		ref int $iterCount
		
	return value
		bool
	*/
	private static function VerifyHashedPasswordV3($hashedPassword, $password, &$iterCount)
	{
		$iterCount = 0;
		
		try {
            // Read header information
			$prf = PasswordHasher::ReadNetworkByteOrder($hashedPassword, 1);
			$iterCount = PasswordHasher::ReadNetworkByteOrder($hashedPassword, 5);
			$saltLength = PasswordHasher::ReadNetworkByteOrder($hashedPassword, 9);
			
			// Read the salt: must be >= 128 bits
			if ($saltLength < 128 / 8) {
				return false;
			}
			$salt = array_fill(0, $saltLength, 0);
			PasswordHasher::BlockCopy($hashedPassword, 13, $salt, 0, count($salt));
			
			// Read the subkey (the rest of the payload): must be >= 128 bits
			$subkeyLength = count($hashedPassword) - 13 - count($salt);
			if ($subkeyLength < 128 / 8) {
				return false;
			}
			$expectedSubkey = array_fill(0, $subkeyLength, 0);
			PasswordHasher::BlockCopy($hashedPassword, 13 + count($salt), $expectedSubkey, 0, count($expectedSubkey));
			
			// Hash the incoming password and verify it
			$actualSubkey = unpack("C*", hash_pbkdf2(PasswordHasher::GetHashAlgoFromPrf($prf), $password, pack("C*", ...$salt), $iterCount, $subkeyLength, true));
			// since unpack will create an array starting with index 1, we need to unshift and shift the array to make its index starts at 0
			array_unshift($actualSubkey, 0);
			array_shift($actualSubkey);
			return PasswordHasher::ByteArraysEqual($actualSubkey, $expectedSubkey);
        }
        catch (Exception $e) {
            // This should never occur except in the case of a malformed payload, where
			// we might go off the end of the array. Regardless, a malformed payload
			// implies verification failed.
            return false;
        }
	}
	
	/*
	params
		byte[] $buffer
		int $offset
		uint value
		
	return value
		void
	*/
	private static function WriteNetworkByteOrder(&$buffer, $offset, $value)
	{
		$buffer[$offset + 0] = ($value >> 24);
		$buffer[$offset + 1] = ($value >> 16);
		$buffer[$offset + 2] = ($value >> 8);
		$buffer[$offset + 3] = ($value >> 0);
	}
	
	/*
	params
		byte[] $buffer
		int $offset
		
	return value
		uint
	*/
	private static function ReadNetworkByteOrder($buffer, $offset) 
	{
		return ($buffer[$offset + 0] << 24)
                | ($buffer[$offset + 1] << 16)
                | ($buffer[$offset + 2] << 8)
                | ($buffer[$offset + 3]);
	}
	
	/*
	params
		byte[] $a
		byte[] $b
		
	return value
		bool
	*/
	private static function ByteArraysEqual($a, $b)
	{
		if (is_null($a) && is_null($b)) {
			return true;
		}
		
		if (is_null($a) || is_null($b) || count($a) != count($b)) {
			return false;
		}
		
		$areSame = true;
		for ($i = 0; $i < count($a); $i++) {
			$areSame = $areSame & ($a[$i] == $b[$i]);
		}
		return $areSame;
	}
	
	/*
	params
		array $src
		int $srcOffset
		array $dst
		int $dstOffset
		int $count
		
	return value
		void
	*/
	private static function BlockCopy($src, $srcOffset, &$dst, $dstOffset, $count)
	{
		for ($i = 0; $i < $count; $i++) {
			$dst[$dstOffset + $i] = $src[$srcOffset + $i];
		}
	}
	
	/*
	params
		int $prf
		
	return value
		string
	*/
	private static function GetHashAlgoFromPrf($prf)
	{
		switch ($prf) {
			case 1:
				return "sha256";
				
			case 2:
				return "sha512";
			
			default:
				return "sha1";
		}
	}
}
?>
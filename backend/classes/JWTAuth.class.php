<?php

require_once 'vendor/autoload.php';

use ReallySimpleJWT\Token;

class JWTAuth
{
    private static $ISSUER = "19com";
    private static $TOKEN_DURATION = "2 hour";
    private static $TOKEN_SECRET = "sec!ReT423*&";

    public static function build($id, $username)
    {
        // Minimal Version
        $tokenDateTime = date('Y-m-d H:i:s', strtotime('+' . self::$TOKEN_DURATION, strtotime(today(false))));
        $tokenDateTime = strtotime($tokenDateTime);

        $token = Token::create($id, self::$TOKEN_SECRET, $tokenDateTime, self::$ISSUER);

        return $token;
    }

    public static function validate()
    {
        // Minimal Version
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
        } else {
            return 0;
        }

        $result = Token::validate($token, self::$TOKEN_SECRET);

        if ($result) {
            $payload = Token::getPayload($token, self::$TOKEN_SECRET);
            return $payload['user_id'];
        } else {
            return 0;
        }

    }


}
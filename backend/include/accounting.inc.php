<?php

	include('model/accounting.php');
	include('model/transaction.php');
	include('model/balance.php');
	include('model/credit.php');
	include('model/level.php');
	include('model/user.php');
	include('model/user_level_up.php');

	function insertTransaction($userId, $amount, $referenceId, $creditId, $fromId, $toId, $remark="", $subject=""){
		$transaction 	= new transaction();
		$credit 		= new credit();
		$date			= date("Y-m-d H:i:s");
		
		//do not process negative value
		if($amount <= 0){
			return;
		}
		
		$transaction->from_id 		= $fromId;
		$transaction->to_id 		= $toId;
		$transaction->user_id		= $userId;
		$transaction->credit_id		= $creditId;
		$transaction->amount 		= $amount;
		$transaction->subject		= $subject;
		$transaction->reference_id	= $referenceId;
		$transaction->remark		= $remark;
		$transaction->created_at	= $date;
		
		$transaction->save();
		
		insertAccounting(0, $amount, $fromId, $toId, $referenceId, $creditId, $date);
		insertAccounting($amount, 0, $toId, $fromId, $referenceId, $creditId, $date);
		
		$user			= new user();
		$userObj		= $user->byId($toId);
		$creditObj		= $credit->byId($creditId);
		if($creditObj->name == 'points'){
			$userObj->total_points	= $userObj->total_points + $amount;
			$userObj->save();
			updateUserLevel($userObj);
		}
		if($creditObj->name == 'voucher'){
			$userObj->total_voucher	= $userObj->total_voucher + $amount;
			$userObj->save();
		}
	}

	function insertAccounting($credit, $debit, $fromId, $toId, $referenceId, $creditId, $date){
		$accounting		 = new accounting();
		if(!$date) $date = date('Y-m-d H:i:s'); 
		
		$accounting->from_id 		= $fromId;
		$accounting->to_id			= $toId;
		$accounting->credit_id		= $creditId;
		$accounting->credit			= $credit;
		$accounting->debit			= $debit;
		$accounting->reference_id	= $referenceId;
		$accounting->created_at		= $date;
		$accounting->save();
		
		updateBalance($fromId, $creditId);
	}

	function updateBalance($userId, $creditId){
		$balance 	= new balance();
		$accounting = new accounting();
		$credit		= new credit();
		$user		= new user();
		$date		= date("Y-m-d");
		
		$current_balance = $accounting->where('from_id', $userId)->where('credit_id', $creditId)->getValue("SUM(credit - debit)");
		$check_balance = $balance->where('user_id', $userId)->where('credit_id', $creditId)->where('date', $date)->getOne();
		if(!$check_balance){
			$balance->credit_id 	= $creditId;
			$balance->user_id		= $userId;
			$balance->date			= $date;
			$balance->created_at	= date('Y-m-d H:i:s');
		}else{
			$balance = $check_balance;
		}
		
		$balance->balance = $current_balance;
		$balance->save();
		
		$creditObj = $credit->byId($creditId);
		$user->updateCustom(array($creditObj->name => $current_balance), array('id' => $userId));
	}

	function updateUserLevel($userObj){
		$level			= new level();
		$level_result	= $level->orderBy('points', 'DESC')->get();
		foreach($level_result as $levelObj){
			if($userObj->total_points >= $levelObj->points){
				$user_level_up	= new user_level_up();
				$user_level_upObj = $user_level_up->where('user_id', $userObj->id)->where('level_id', $levelObj->id)->getOne();
				if(!$user_level_upObj){
					$userObj->level_id	= $levelObj->id;
					$userObj->save();
					
					$user_level_up->user_id		= $userObj->id;
					$user_level_up->level_id	= $levelObj->id;
					$user_level_up->created_at	= date("Y-m-d H:i:s");
					if($levelObj->points == 0){
						$user_level_up->claimed	= 1;
					}
					$user_level_up->save();
				}
				break;
			}
		}
	}

	function getBalance($userId, $creditId, $date=""){
		$balance = new balance();
		
		if(!$date)
			$current_balance = $balance->where('user_id', $userId)->where('credit_id', $creditId)->orderBy('date', 'desc')->getValue('balance');
		else
			$current_balance = $balance->where('user_id', $userId)->where('credit_id', $creditId)->where('date', $date)->getValue('balance');
		
		return $current_balance ? $current_balance : '0.00';
	}

?>
<?php
//this is an api to like question

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");
require_once("../GCM.php");
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'root', 'core2duo', 'codebrew_naseeb');
error_reporting(0);


$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['token'];
$user_id2=$_REQUEST['user_id2'];
$flag=$_REQUEST['flag'];

if(!($token && $user_id2)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	//fetching user_id and name based on token
	$sql="SELECT * from users where users.token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('token',$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	$uname=$result[0]['username'];
	
	if($user_id){
	
	$sql="SELECT * from user_like where liked_by=:user_id and liked_to=:user_id2";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();}
	catch(Exception $e){}
	$likes=$sth->fetchAll();
	
	
	$other_like_status=GeneralFunctions::getLikeStatus($user_id,$user_id2);
	$push_check=GeneralFunctions::PushCheck($user_id2);
	$new_match=$push_check[0]['new_matches'];
	
	
	//push notification code
	$user=GeneralFunctions::get_push_ids($user_id2);
	$apnid=$user[0]['apn_id'];
	$oid=$user[0]['id'];
	$reg_ids[]=$user[0]['reg_id'];
	
	$message=array();
	$message['msg']= $uname. ' and you have a match';
	$message['type']='match';//match-push
	$message['uid']=$user_id;
	$type='match';
	
	if(count($likes)){
	
	$sql="UPDATE user_like set status=:status where liked_by=:user_id and liked_to=:user_id2";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('user_id2',$user_id2);
	$sth->bindValue('status',$flag);
	try{ 
	$sth->execute();
	$success="1";
	$msg="Status Updated";
	
	if($new_match){
	if($other_like_status=='1' && $flag='1'){
	
		if(!empty($reg_ids)){	
	    GCM::send_notification($reg_ids, $message);
		}
				
		if(!empty($apnid)){
			try{
			$apns->newMessage($apnid);
			$apns->addMessageAlert($message['msg']);
			$apns->addMessageSound('x.wav');
			$apns->addMessageCustom('u', $user_id);
			$apns->addMessageCustom('t', $message['type']);
			//$apns->addMessageCustom('x', $profile_pic);
			$apns->queueMessage();
			$apns->processQueue();
			}
			catch(Exception $e){}	
		}
	
	}
	}
	
	
	}
	catch(Exception $e){}
	}
	else{
		
	$sql="INSERT into user_like(id,liked_by,liked_to,status,created_on) values(DEFAULT,:user_id,:user_id2,:status,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('user_id2',$user_id2);
	$sth->bindValue('status',$flag);
	try{ 
	$sth->execute();
	$success="1";
	$msg="Status Updated";
	
		if($new_match){
		if($other_like_status=='1' && $flag='1'){
	
		if(!empty($reg_ids)){	
	    GCM::send_notification($reg_ids, $message);
		}
				
		if(!empty($apnid)){
			try{
			$apns->newMessage($apnid);
			$apns->addMessageAlert($message['msg']);
			$apns->addMessageSound('x.wav');
			$apns->addMessageCustom('u', $user_id);
			$apns->addMessageCustom('t', $message['type']);
			//$apns->addMessageCustom('x', $profile_pic);
			$apns->queueMessage();
			$apns->processQueue();
			}
			catch(Exception $e){}	
		}
	
	}
	}
	}
	catch(Exception $e){}
	}
	}
	else{
	$success='0';
	$msg="Token Expired";
	}	
}
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+

echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
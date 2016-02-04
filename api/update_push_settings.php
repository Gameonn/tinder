<?php
//this is an api to update user setting parameters

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('../classes/AllClasses.php');


$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['token'];
$new_matches=$_REQUEST['new_matches'];
$push_notification=$_REQUEST['push_notification'];


if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

	$uid=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	
	if($uid){
		

	$sth=$conn->prepare("UPDATE user_setting set new_matches=:new_matches,push_notification=:push_notification WHERE user_id=:user_id");
	
	$sth->bindValue('user_id',$uid);
	$sth->bindValue("new_matches",$new_matches);
	$sth->bindValue("push_notification",$push_notification);
	try{$sth->execute();
	$success='1';
	$msg="User Notification Settings Updated";
	$data=Users::fbsigninNew($fbid);
	}
	catch(Exception $e){}	
		

	}
	else{
	$success='0';
	$msg="Token Expired";
	}
	
}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
<?php
//this is an api to get messages conversation between users

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['token'];
$user2=$_REQUEST['user_id2'];
$message_id=$_REQUEST['message_id'];
$GLOBALS['timezone']=$_REQUEST['timezone'];

if(!($token && $user2 && $message_id)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$user_id=GeneralFunctions::getUserId($token);
	
	if($user_id){
	$data= Messages::getUserMessagesBefore($user_id,$user2,$message_id);
		
		if($data){
		$success="1";
		$msg="Records Found";
		}
		else{
		$success='0';
		$msg="No Record Found";
		}
	}
	else{
	$success="0";
	$msg="Token Expired";
	}	
}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
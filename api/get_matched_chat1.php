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

if(!($token )){
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
	
	$data['messages']= Messages::getMatchedChat($user_id)?Messages::getMatchedChat($user_id):[];
	
	$data['users']=GeneralFunctions::check_match($user_id)?GeneralFunctions::check_match($user_id):[];
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
echo json_encode(array("success"=>$success,"msg"=>$msg,"messages"=>$data['messages'],"users"=>$data['users']));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
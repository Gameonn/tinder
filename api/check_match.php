<?php
//this is an api to check match

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
if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	//fetching user_id and name based on token
	$user_id=Users::getUserId($token);
	
	if($user_id){
	
	$match=GeneralFunctions::check_match($user_id)?GeneralFunctions::check_match($user_id):[];
	if($match){
	$success='1';
	$msg="Matches Found";
	}
	else{
	$success='1';
	$msg="No Match Found";
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

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'match'=>$match));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));
?>
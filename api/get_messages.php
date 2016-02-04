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
$other_id=$_REQUEST['user_id2'];
$GLOBALS['timezone']=$_REQUEST['timezone'];

if(!($token && $other_id)){
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
	
	//set messages received before the specified message_id as read
	$updated=Messages::updateReadStatus($user_id,$other_id);
	
	$geo_result=Users::getLatLong($user_id);
	$lat=$geo_result[0]['lat'];
	$lang=$geo_result[0]['lang'];
	
	$profile=Users::getOtherProfile($user_id,$other_id,$lat,$lang)?Users::getOtherProfile($user_id,$other_id,$lat,$lang):[];
	$data= Messages::getUserMessages($user_id,$other_id)?Messages::getUserMessages($user_id,$other_id):[];
	
	$success="1";
	$msg="Success";
	
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
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data,"profile"=>$profile));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
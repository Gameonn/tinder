<?php
//this is an api to update last visited time

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

	$uid=Users::getUserId($token);
	
	
	if($uid){
		
	$updated= Users::UpdateLastVisitedTime($uid);
	if($updated){
	$success="1";
	$msg="Last Visited Time Updated";
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
/*if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else*/
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
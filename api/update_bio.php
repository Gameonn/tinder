<?php
//this is an api to update user bio

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
$bio=$_REQUEST['bio']?$_REQUEST['bio']:"";


if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

	$uid=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	
	if($uid){
		
	$sth=$conn->prepare("UPDATE users set bio=:bio WHERE token=:token");
	$sth->bindValue('token',$token);
	$sth->bindValue("bio",$bio);
	try{$sth->execute();
	$success='1';
	$msg="User Bio Updated";
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
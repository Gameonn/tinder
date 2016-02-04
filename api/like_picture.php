<?php
//this is an api to like question

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
$gallery_id=$_REQUEST['image_id'];
$flag=$_REQUEST['flag']?$_REQUEST['flag']:'1';
if(!($token && $gallery_id)){
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
	
	$sql="select * from picture_like where user_id=:user_id and gallery_id=:gallery_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('gallery_id',$gallery_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$likes=$sth->fetchAll();
	
	if(count($likes)){
	$sql="UPDATE picture_like set status=:status where user_id=:user_id and gallery_id=:gallery_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('gallery_id',$gallery_id);
	$sth->bindValue('status',$flag);
	try{ 
	$sth->execute();
	$success="1";
	$msg="Status Updated";
	}
	catch(Exception $e){}
	}
	else{
	
	$sql="Insert into picture_likes(id,user_id,gallery_id,status,created_on) values(DEFAULT,:uid,:gallery_id,:status,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('gallery_id',$gallery_id);
	$sth->bindValue('status',$flag);
	try{ 
	$sth->execute();
	$like_id=$conn->lastInsertId();
	$success="1";
	$msg="Status set for this picture";
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
/*if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"like_id"=>$like_id));
}
else*/
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
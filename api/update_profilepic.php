<?php
//this is an api to update user setting parameters

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('../classes/AllClasses.php');

//random file name generator
function randomFileNameGenerator($prefix){
	$r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
	if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
	else return $r;
}


$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['token'];
$profile_pic=$_REQUEST['image'];


if(!($token && $profile_pic)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

	$uid=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	
	if($uid){
	
	$image=str_replace('http://52.26.234.175/uploads/','',$profile_pic);
	
	
	$gallery_pic=Users::getCurrentPic($uid);
	$new_image_id=Users::getPicId($image);//new profile image
	$old_image_id=Users::getPicId($gallery_pic);//previous profile image
	
	
	$sql="UPDATE gallery set is_profile_pic=0 WHERE user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("user_id",$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	
	$sql="UPDATE gallery set is_profile_pic=1 WHERE image='{$image}' and user_id='{$uid}'";
	$sth=$conn->prepare($sql);
	//$sth->bindValue('new_image',$image);
	//$sth->bindValue("old_image",$gallery_pic);
	//$sth->bindValue("user_id",$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	
	
	$sth=$conn->prepare("UPDATE users set profile_pic=:profile_pic WHERE token=:token");
	$sth->bindValue('token',$token);
	$sth->bindValue("profile_pic",$image);
	try{$sth->execute();
	$success='1';
	$msg="Profile Pic Updated";
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
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
$discovery=$_REQUEST['discovery']?$_REQUEST['discovery']:1;
$distance=$_REQUEST['distance']?$_REQUEST['distance']:100;
$interested=$_REQUEST['interested_in']?$_REQUEST['interested_in']:'Women';
$min_age = $_REQUEST['min_age']?$_REQUEST['min_age']:18;
$max_age = $_REQUEST['max_age']?$_REQUEST['max_age']:55;
$education_id = $_REQUEST['education_id']?$_REQUEST['education_id']:'4';
$profession_id = $_REQUEST['profession_id']?$_REQUEST['profession_id']:'4';
$relation_id = $_REQUEST['relation_id']?$_REQUEST['relation_id']:'4';
$food_pref_id = $_REQUEST['food_pref_id']?$_REQUEST['food_pref_id']:'4';
$drinking_id = $_REQUEST['drinking_id']?$_REQUEST['drinking_id']:'4';
$smoking_id = $_REQUEST['smoking_id']?$_REQUEST['smoking_id']:'4';
$religion_id = $_REQUEST['religion_id']?$_REQUEST['religion_id']:'4';
$dating_pref_id = $_REQUEST['dating_pref_id']?$_REQUEST['dating_pref_id']:'4';
$sexual_pref_id= $_REQUEST['sexual_pref_id']?$_REQUEST['sexual_pref_id']:'4';
$height= $_REQUEST['height']?$_REQUEST['height']:'Any';
$profile_pic=$_FILES['profile_pic'];
$bio=$_REQUEST['bio']?$_REQUEST['bio']:'';
$gender=$_REQUEST['gender']?$_REQUEST['gender']:'';
$age=$_REQUEST['age']?$_REQUEST['age']:'';
$city=$_REQUEST['city']?$_REQUEST['city']:'';

if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

	$uid=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	
	if($uid){
	
		if($profile_pic){
	$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$profile_pic['name']));
			if(@move_uploaded_file($profile_pic['tmp_name'], "../uploads/$randomFileName")){
				$profile_pic_path=$randomFileName;
		}
	}
	else{
	$profile_pic_path="";
	}
	
	$updated=Users::UpdatePartnerPreferences($uid,$min_age,$max_age,$education_id,$profession_id,$relation_id,$food_pref_id, $drinking_id, $smoking_id, $religion_id, $dating_pref_id, $sexual_pref_id,$height);
		
	if($profile_pic_path)
	$sth=$conn->prepare("update users set bio=:bio,profile_pic=:profile_pic,gender=:gender,age=:age,city=:city where token=:token");
	else
	$sth=$conn->prepare("update users set bio=:bio,gender=:gender,city=:city,age=:age where token=:token");	
	
	$sth->bindValue('token',$token);
	if($profile_pic_path) $sth->bindValue("profile_pic",$profile_pic_path);
	$sth->bindValue('gender',$gender);
	$sth->bindValue("bio",$bio);
	$sth->bindValue("age",$age);
	$sth->bindValue("city",$city);
	try{$sth->execute();}
	catch(Exception $e){}	
		
	$sql="select * from user_setting where user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	if(count($res)){
	
	$sql="UPDATE user_setting set discovery=:discovery,distance=:distance,interested_in=:interested_in where user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('discovery',$discovery);
	$sth->bindValue('distance',$distance);
	$sth->bindValue('interested_in',$interested);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();
	$success='1';
	$msg="Settings Updated Successfully";
	$data=Users::fbsigninNew($fbid);
	}
	catch(Exception $e){}
	
	}
	else{
		
	$sql="INSERT INTO `user_setting` (`id`, `user_id`, `discovery`, `new_matches`, `push_notification`,`distance`,`interested_in`,`created_on`) VALUES (DEFAULT, :user_id, :discovery,1,1,:distance,:interested_in,NOW())";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('discovery',$discovery);
	$sth->bindValue('interested_in',$interested);
	$sth->bindValue('distance',$distance);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();
	$success='1';
	$msg="Settings Updated Successfully";
	$data=Users::fbsigninNew($fbid);
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
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
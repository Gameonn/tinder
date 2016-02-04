<?php
//this is an api to register users using facebook on the server

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");
error_reporting(0);
//random file name generator
function randomFileNameGenerator($prefix){
	$r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
	if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
	else return $r;
}

//date conversion function
function convertDate($date) {
	$date = preg_replace('/\D/','/',$date);
	return date('Y-m-d',strtotime($date));
}


$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$fbid=$_REQUEST['fbid'];
$email=$_REQUEST['email']?$_REQUEST['email']:"";
$bio=$_REQUEST['bio']?$_REQUEST['bio']:"";
$fname=$_REQUEST['name']?$_REQUEST['name']:"";//fullname
if($fname){
$element = explode(' ', $fname);
$name=$element[0];
}
else
$name="";

$gender=$_REQUEST['gender']?$_REQUEST['gender']:"";
$lat=$_REQUEST['lat']?$_REQUEST['lat']:"";
$lang=$_REQUEST['lang']?$_REQUEST['lang']:"";
$dob1=$_REQUEST['dob']?$_REQUEST['dob']:'1991-01-12';

$dob=convertDate($dob1);

$age = (int)((time()- strtotime ($dob)) /(3600 * 24 * 365));//age calculation formula

if($age<18)
$age=18;

$city=$_REQUEST['city']?$_REQUEST['city']:"";
$profile_pic=$_FILES['profile_pic'];

//$image=$_FILES['image'];
$apnid=$_REQUEST['apn_id']?$_REQUEST['apn_id']:0;
$regid=$_REQUEST['reg_id']?$_REQUEST['reg_id']:"";
$friend=$_REQUEST['facebook_friends'];
$fb_token=$_REQUEST['fb_token'];

if($gender=='female')
	$interested='Men';
else
	$interested='Women';

global $conn;
	$option = new Options;
// +-----------------------------------+
// +  Mandatory Parameters				   +
// +-----------------------------------+
if(!($fbid)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{ 	
	
	//checking the existence of fbid entered
	$fbid_exists=Users::checkfb($fbid);
	
	//updating user details for existing email
	if($fbid_exists){
	
	$uid=Users::checkuserid($fbid);
	
	$friends_saved= Users::AddFriendsLogin($uid,$friend);
		
	$interest=Users::getInterestJson($fb_token,$fbid);
	
	$interest_saved= Users::AddCommonInterests($uid,$interest);
	
	/*$gallery_count=Users::getGalleryCount($uid);
		
	if($gallery_count<6){
		//fetching all albums of user
	$alb=Users::getAlbumsJson($fb_token,$fbid);
	
	$output = json_decode($alb,true);
	
	//fetching album id for profile pictures
	foreach($output['data'] as $k=>$row){
		if($row['name']=="Profile Pictures")
		$album_id = $row['id'];
		}
	
	//fetching images of user based on fbid		
	$albums=Users::getPhotosJson($fb_token,$album_id);
	
	//saving images fetched in album
	$albums_saved= Users::AddPhotos($uid,$albums,$fb_token);
	}*/
	
	$data= Users::fbsignin($fbid);
	$user_id = $data['user_id'];
	$friend_count = GeneralFunctions::getAllFriendsCount($user_id);
	$partner_pref=Users::getPartnerPref($user_id);
	$success="1";
	$msg="Login Successful";
	$login_parameter='2';
	
	//all category based options
	$religion=$option->getReligionOption();
	$relation=$option->getRelationOption();
	$education=$option->getEducationOption();
	$profession=$option->getProfessionOption();
	$dating_pref=$option->getDatingPref();
	$food_pref=$option->getFoodPref();
	$sexual_pref=$option->getSexualPref();
	$smoking=$option->getSmokingOption();
	$drinking=$option->getDrinkingOption();
	$gender= $option->getGender();
	$height=$option->getHeight();
	$age=$option->getAge();
	$city=$option->getCity();
	
	$preference_listing['religion']=$religion;
	$preference_listing['relation']=$relation;
	$preference_listing['education']=$education;
	$preference_listing['profession']=$profession;
	$preference_listing['dating_pref']=$dating_pref;
	$preference_listing['food_pref']=$food_pref;
	$preference_listing['sexual_pref']=$sexual_pref;
	$preference_listing['smoking']=$smoking;
	$preference_listing['drinking']=$drinking;
	$preference_listing['gender']=$gender;
	$preference_listing['height']=$height;
	$preference_listing['age']=$age;
	$preference_listing['city']=$city;
	}		
	
	//New User Entry
	else{	
		
		//uploading profile_pic
		$profile_pic = file_get_contents('https://graph.facebook.com/'.$fbid.'/picture?width=1024&height=1024');
		$profile_pic_name = 'IMG_'.$fbid.'.jpg';
		file_put_contents("../uploads/".$profile_pic_name, $profile_pic);
		
			
		//generating a new random token for that user 
		$code= Users::generateRandomString(12);
	$sql="INSERT into users(id,fbid,apn_id,reg_id,username,email,password,token,gender,profile_pic,bio,age,city,lat,lang,created_on,last_visited) 
	values(DEFAULT,:fbid,:apn_id,:reg_id,:name,:email,'',:token,:gender,:profile_pic,:bio,:age,:city,:lat,:lang,NOW(),NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("email",$email);
		$sth->bindValue('fbid',$fbid);
		$sth->bindValue("bio",$bio);
		$sth->bindValue("name",$name);
		$sth->bindValue("gender",$gender);
		$sth->bindValue("apn_id",$apnid);
		$sth->bindValue("reg_id",$regid);
		$sth->bindValue("age",$age);
		$sth->bindValue("city",$city);
		$sth->bindValue("lat",$lat);
		$sth->bindValue("lang",$lang);
		$sth->bindValue("profile_pic",$profile_pic_name);
		$sth->bindValue("token",md5($code));
		try{$sth->execute();
		$uid=$conn->lastInsertId();
		$success='1';
		$msg="User Successfully registered";
		//$data=Users::fbsignin($fbid);
		}
		catch(Exception $e){}	
		
		//user personal details fetched from fb
		$sql="INSERT into user_detail(id,user_id,sexual_pref_id,height,education_id,profession_id,relation_id,food_pref_id,drinking_id,smoking_id,religion_id, dating_pref_id, employment_place,created_on) VALUES(DEFAULT,:user_id,4,'Any',4,4,4,4,4,4,4,4,'', NOW())";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$uid);
		try{$sth->execute();}
		catch(Exception $e){}
		
		//user partner preference set as default
		$sql="INSERT into partner_pref(id,user_id,min_age,max_age,education_id,profession_id,sexual_pref_id,height,relation_id,food_pref_id,drinking_id,smoking_id,religion_id, dating_pref_id,created_on) VALUES(DEFAULT,:user_id,18,45,4,4,4,'Any',4,4,4,4,4,4,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$uid);
		try{$sth->execute();}
		catch(Exception $e){}	
		
		
		//adding profile pic to gallery
		$sql="INSERT into gallery(id,user_id,fb_photo_id,image,is_profile_pic,created_on) VALUES(DEFAULT,:user_id,:fbid,:profile_pic,1,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$uid);
		$sth->bindValue("fbid",$fbid);
		$sth->bindValue("profile_pic",$profile_pic_name);
		try{$sth->execute();}
		catch(Exception $e){}	
				
		$sql="INSERT into user_setting(id,user_id,discovery,new_matches,push_notification,distance,interested_in,check_parameter,profile_parameter,created_on) VALUES(DEFAULT,:user_id,1,1,1,2500,:interested_in,0,0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$uid);
		$sth->bindValue("interested_in",$interested);
		try{$sth->execute();}
		catch(Exception $e){}

		
		//adding image uploads to gallery
		if($fb_token){	
		
		//fetching all albums of user
		$alb=Users::getAlbumsJson($fb_token,$fbid);
		
		$output = json_decode($alb,true);
		
		//fetching album id for profile pictures
		foreach($output['data'] as $k=>$row){
			if($row['name']=="Profile Pictures")
			$album_id = $row['id'];
		
		}
		
		//fetching images of user based on fbid		
		$albums=Users::getPhotosJson($fb_token,$album_id);
		
		//saving images fetched in album
		$albums_saved= Users::AddPhotos($uid,$albums,$fb_token);
		}
		
		$fb_friends=json_decode($friend,true);
		if($fb_friends){
		foreach($fb_friends as $k=>$row){
		
		$sql="SELECT * FROM `facebook_friends` WHERE user_id=:user_id and friend_fbid=:friend_fbid";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$uid);
		$sth->bindValue('friend_fbid',$row['id']);	
		try{$sth->execute();}
		catch(PDOException $e){}
		$result[$k]=$sth->fetchAll();
		
		$fb_image='https://graph.facebook.com/'.$row["id"].'/picture';
		
		if(!count($result[$k])){
		$sql="INSERT INTO `facebook_friends`(id,user_id,friend_fbid,name,fb_image,created_on) VALUES(DEFAULT,:user_id,:friend_fbid,:name,:fb_image,NOW())";
		$sth = $conn->prepare($sql);
		$sth->bindValue('name',$row['name']);
		$sth->bindValue('user_id',$uid);
		$sth->bindValue('friend_fbid',$row['id']);
	    $sth->bindValue('fb_image',$fb_image );	
		try{$sth->execute();}
		catch(PDOException $e){}
		}
		}
		}
		
		if($fb_token){
		$interest=Users::getInterestJson($fb_token,$fbid);
		
		$interest_saved= Users::AddCommonInterests($uid,$interest);
		}
		
		if($success){
		$data=Users::fbsigninNew($fbid);
		$user_id = $data['user_id'];
		$friend_count = GeneralFunctions::getAllFriendsCount($user_id);
		$partner_pref=Users::getPartnerPref($user_id);
		$login_parameter='1';
		
		//all category based options
		$religion=$option->getReligionOption();
		$relation=$option->getRelationOption();
		$education=$option->getEducationOption();
		$profession=$option->getProfessionOption();
		$dating_pref=$option->getDatingPref();
		$food_pref=$option->getFoodPref();
		$sexual_pref=$option->getSexualPref();
		$smoking=$option->getSmokingOption();
		$drinking=$option->getDrinkingOption();
		$gender= $option->getGender();
		$height=$option->getHeight();
		$age=$option->getAge();
		$city=$option->getCity();
		
		$preference_listing['religion']=$religion;
		$preference_listing['relation']=$relation;
		$preference_listing['education']=$education;
		$preference_listing['profession']=$profession;
		$preference_listing['dating_pref']=$dating_pref;
		$preference_listing['food_pref']=$food_pref;
		$preference_listing['sexual_pref']=$sexual_pref;
		$preference_listing['smoking']=$smoking;
		$preference_listing['drinking']=$drinking;
		$preference_listing['gender']=$gender;
		$preference_listing['height']=$height;
		$preference_listing['age']=$age;
		$preference_listing['city']=$city;
		}
	}	
}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"profile"=>$data,'friend_count'=>$friend_count,'partner_pref'=>$partner_pref,'login_parameter'=>$login_parameter,"preference_listing"=>$preference_listing));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
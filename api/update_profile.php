<?php
//this is an api to update user profile

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('../classes/AllClasses.php');


//date conversion function
function convertDate($date) {
	$date = preg_replace('/\D/','/',$date);
	return date('Y-m-d',strtotime($date));
}


$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['token'];
$education = $_REQUEST['education_id']?$_REQUEST['education_id']:'Any';
$profession = $_REQUEST['profession_id']?$_REQUEST['profession_id']:'Any';
$relation = $_REQUEST['relation_id']?$_REQUEST['relation_id']:'Any';
$food_pref = $_REQUEST['food_pref_id']?$_REQUEST['food_pref_id']:'Any';
$drinking = $_REQUEST['drinking_id']?$_REQUEST['drinking_id']:'Any';
$smoking = $_REQUEST['smoking_id']?$_REQUEST['smoking_id']:'Any';
$religion = $_REQUEST['religion_id']?$_REQUEST['religion_id']:'Any';
$dating_pref = $_REQUEST['dating_pref_id']?$_REQUEST['dating_pref_id']:'Any';
$sexual_pref = $_REQUEST['sexual_pref_id']?$_REQUEST['sexual_pref_id']:'Any';
$employment_place=$_REQUEST['employment_place']?$_REQUEST['employment_place']:'';
$interested = $_REQUEST['interested_in']?$_REQUEST['interested_in']:'female';//receiving gender values in interested_in parameter- using it differently
$height= $_REQUEST['height']?$_REQUEST['height']:'Any';
$gender=$_REQUEST['gender']?$_REQUEST['gender']:'';
$dob1=$_REQUEST['dob']?$_REQUEST['dob']:'1991-01-12';
$dob= date('Y-m-d',strtotime($dob1));
//$dob=convertDate($dob1);

$city=$_REQUEST['city']?$_REQUEST['city']:'';

if($interested=='male')
	$interested_in='Men';
else
	$interested_in='Women';

if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

	$uid=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	if($uid){
		
	$education_id=Options::getEducationOptionId($education);
	$profession_id=Options::getProfessionOptionId($profession);
	$relation_id=Options::getRelationOptionId($relation);
	$food_pref_id=Options::getFoodPrefId($food_pref);
	$drinking_id=Options::getDrinkingOptionId($drinking);
	$smoking_id=Options::getSmokingOptionId($smoking);
	$religion_id=Options::getReligionOptionId($religion);
	$dating_pref_id=Options::getDatingPrefId($dating_pref);
	$sexual_pref_id=Options::getSexualPrefId($sexual_pref);
	
	
	 $updated=Users::UpdateUserDetail($uid,$education_id,$profession_id,$relation_id,$food_pref_id, $drinking_id, $smoking_id, $religion_id, $dating_pref_id, $sexual_pref_id,$height,$employment_place);
	
	/*if($sexual_pref_id==2 || $sexual_pref_id==3){
		
		if($gender=='male') 
		  $interested_in='Men';
		else
		  $interested_in='Women';*/
	
	$sth=$conn->prepare("UPDATE user_setting set interested_in=:interested_in,profile_parameter=1 where user_id=:user_id");
	$sth->bindValue('user_id',$uid);
	$sth->bindValue("interested_in",$interested_in);
	try{$sth->execute();}
	catch(Exception $e){}
	//}	
		
	$sth=$conn->prepare("update users set gender=:gender,age=YEAR(NOW())-YEAR(:dob),city=:city where token=:token");
	$sth->bindValue('token',$token);
	$sth->bindValue('gender',$gender);
	$sth->bindValue("dob",$dob);
	$sth->bindValue("city",$city);
	try{$sth->execute();
	$success='1';
	$msg="Success";
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

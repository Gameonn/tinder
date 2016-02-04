<?php
require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$education = $_REQUEST['education_id']?$_REQUEST['education_id']:'Any';
$profession = $_REQUEST['profession_id']?$_REQUEST['profession_id']:'Any';
$relation = $_REQUEST['relation_id']?$_REQUEST['relation_id']:'Any';
$food_pref = $_REQUEST['food_pref_id']?$_REQUEST['food_pref_id']:'Any';
$drinking = $_REQUEST['drinking_id']?$_REQUEST['drinking_id']:'Any';
$smoking = $_REQUEST['smoking_id']?$_REQUEST['smoking_id']:'Any';
$religion = $_REQUEST['religion_id']?$_REQUEST['religion_id']:'Any';
$dating_pref = $_REQUEST['dating_pref_id']?$_REQUEST['dating_pref_id']:'Any';
$sexual_pref = $_REQUEST['sexual_pref_id']?$_REQUEST['sexual_pref_id']:'Any';
$height= $_REQUEST['height']?$_REQUEST['height']:'Any';
$discovery=$_REQUEST['discovery'];
$distance=$_REQUEST['distance']?$_REQUEST['distance']:1500;
$interested=$_REQUEST['interested_in']?$_REQUEST['interested_in']:'Women';
$min_age = $_REQUEST['min_age']?$_REQUEST['min_age']:18;
$max_age = $_REQUEST['max_age']?$_REQUEST['max_age']:45;
$check_parameter=$_REQUEST['check_parameter']?$_REQUEST['check_parameter']:'0';

if(!$token){
	$success='0';
	$msg='Incomplete Parameters';
}
else{
	
	$user_id=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	
	if(!empty($user_id)){
		
			$education_id=Options::getEducationOptionId($education);
			$profession_id=Options::getProfessionOptionId($profession);
			$relation_id=Options::getRelationOptionId($relation);
			$food_pref_id=Options::getFoodPrefId($food_pref);
			$drinking_id=Options::getDrinkingOptionId($drinking);
			$smoking_id=Options::getSmokingOptionId($smoking);
			$religion_id=Options::getReligionOptionId($religion);
			$dating_pref_id=Options::getDatingPrefId($dating_pref);
			$sexual_pref_id=Options::getSexualPrefId($sexual_pref);
	
	
			$sql="UPDATE user_setting set discovery=:discovery,distance=:distance,interested_in=:interested_in,check_parameter=:check_parameter where user_id=:user_id";
			$sth=$conn->prepare($sql);
			$sth->bindValue('discovery',$discovery);
			$sth->bindValue('distance',$distance);
			$sth->bindValue('interested_in',$interested);
			$sth->bindValue('check_parameter',$check_parameter);
			$sth->bindValue('user_id',$user_id);
			try{$sth->execute();}
			catch(Exception $e){}
		
		$sql="SELECT * from partner_pref where user_id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll();
		
		if(!count($result)){
		$saved=Users::savePartnerPreferences($user_id,$min_age,$max_age,$education_id,$profession_id,$relation_id,$food_pref_id,$drinking_id,$smoking_id,$religion_id, 
		$dating_pref_id, $sexual_pref_id,$height);
		
		if($saved){
			$partner_pref= Users::getPartnerPref($user_id);
			$data=Users::fbsigninNew($fbid);
			$success='1';$msg='Partner preferences saved';
		}else{
			$success='0';$msg='Error saving partner preferences';
		}
		
		}
		else{
		
		$updated=Users::UpdatePartnerPreferences($user_id,$min_age,$max_age,$education_id,$profession_id,$relation_id,$food_pref_id, $drinking_id, $smoking_id, $religion_id, $dating_pref_id, $sexual_pref_id,$height);
		
		if($updated){
			$partner_pref= Users::getPartnerPref($user_id);
			$data=Users::fbsigninNew($fbid);
			$success='1';$msg='Partner preferences updated';
		}else{
			$success='0';$msg='Error updating partner preferences';
		}
		}
	}
	else{
		$success='0';
		$msg="Token Expired";
	}	
}

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'partner_pref'=>$partner_pref,"profile"=>$data,));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));
?>
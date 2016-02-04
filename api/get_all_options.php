<?php
//fetching all predefined categories

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$success=$msg="0";$data=array();

	$option = new Options;

	
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
	$success='1';$msg='success';

	echo json_encode(array('success'=>$success,'msg'=>$msg,'religion'=>$religion,'relation'=>$relation,'education'=>$education,'profession'=>$profession, 
	'dating_pref'=>$dating_pref, 'food_pref'=>$food_pref, 'sexual_pref'=>$sexual_pref, 'smoking'=>$smoking, 'drinking'=>$drinking,'gender'=>$gender,
	'age'=>$age, 'height'=>$height, 'city'=>$city));


?>

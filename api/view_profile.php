<?php
//fetching own profile

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$current_time=time();
$success=$msg="0";$data=array();

if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{

	$user_id=Users::getUserId($token);

	if($user_id){
	
		//$gallery = Users::getUserGallery($user_id);
		$profile = Users::getUserProfile($user_id);
		$friends = GeneralFunctions::getFriendsId($user_id);
		$partner_pref = Users::getPartnerPref($user_id);
	
		$success='1';
		$msg="User Profile";
	
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'profile'=>$profile,'mutual_friends'=>$friends,'partner_pref'=>$partner_pref));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));

?>

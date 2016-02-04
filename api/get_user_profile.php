<?php
//fetching user profile 

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$user_id2 = $_REQUEST['user_id2'];
$lat1 = $_REQUEST['lat'];
$lang1 = $_REQUEST['lang'];
$current_time=time();
$success=$msg="0";$data=array();

if(!($token && $user_id2)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{

	$user_id=Users::getUserId($token);

	if($user_id){
		
		if(!$lat1 && $lang1){
		$lat=$lat1;
		$lang=$lang1;
		$updated= Users::UpdateLatlang($user_id,$lat,$lang);
		}
		else{
		$user_latlong=Users::getLatLong($user_id);
		$lat=$user_latlong[0]['lat'];
		$lang=$user_latlong[0]['lang'];
		}
	
		//$gallery = Users::getUserGallery($user_id2);
		$profile = Users::getOtherProfile($user_id,$user_id2,$lat,$lang);
		$common_interests=Users::getCommonInterests($user_id,$user_id2);
		$mutual_friends = GeneralFunctions::getMutualFriendsList($user_id,$user_id2);
		//$partner_pref = Users::getPartnerPref($user_id2);
	
		$success='1';
		$msg="User Profile";
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'profile'=>$profile,'common_interests'=>$common_interests,'mutual_friends'=>$mutual_friends));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));

?>

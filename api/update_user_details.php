<?php
//fetching user profile 

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$fb_token = $_REQUEST['fb_token'];
$current_time=time();
$success=$msg="0";$data=array();

if(!($token && $fb_token)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{

	$user_id=Users::getUserId($token);
	$uid=$user_id;
	if($user_id){
	
		 $fbid=Users::getUserfbId($token);
	
		//adding friends to list
		if($fb_token){
		
		//fetching friends json using graph api
		$friends=Users::getFriendsJson($fb_token,$fbid);
		
		//saving friends in db
		$friends_saved= Users::AddFriends($user_id,$friends);
		
		
		/*$gallery_count=Users::getGalleryCount($user_id);
		
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
		
		//fetching interests json
		$interest=Users::getInterestJson($fb_token,$fbid);
		
		//saving interests 
		$interest_saved= Users::AddCommonInterests($uid,$interest);
		
		
		}
		
		$success='1';
		$msg="Updated";
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}

if($success){
	$profile = Users::myprofile($user_id);
	$friend_count = GeneralFunctions::getAllFriendsCount($user_id);
echo json_encode(array('success'=>$success,'msg'=>$msg,'profile'=>$profile,'friend_count'=>$friend_count));
}
else{
echo json_encode(array('success'=>$success,'msg'=>$msg));
}

?>

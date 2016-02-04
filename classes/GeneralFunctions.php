<?php
class GeneralFunctions
{
	public static function getImagePath($file_name){
		if(!empty($file_name)){
			return BASE_PATH."uploads/".$file_name;//original path
				//return BASE_PATH."timthumb.php?src=uploads/".$file_name; //timthumb path
		}
		else{
				return BASE_PATH."uploads/no_image.png";
				//return BASE_PATH."timthumb.php?src=uploads/default_256.png"; 	//timthumb path
				
		}
	}
	
	public static function getBasePath(){
	return BASE_PATH."/timthumb.php?src=uploads/";
	}
	
	public static function getUserId($token){
	global $conn;
	
	$sql="select * from users where users.token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('token',$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	
	return $user_id;
	}
	
	
	public static function RemoveMatch($user_id,$other_id){
	
	global $conn;
	$sql="DELETE FROM user_like WHERE liked_by=:user_id AND liked_to=:other_id ";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('other_id',$other_id);
	try{$sth->execute();}
	catch(Exception $e){}
	
	return true;
	}
	
	
	public static function RemoveFavorites($user_id,$other_id){
	
	global $conn;
	$sql="DELETE FROM favorite WHERE fav_by=:user_id AND fav_to=:other_id ";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('other_id',$other_id);
	try{$sth->execute();}
	catch(Exception $e){}
	
	return true;
	}
	
	public static function RemoveConversation($user_id,$other_id){
	
	global $conn;
	$sql="DELETE FROM messages WHERE user_id_sender IN (:user_id,:other_id) and user_id_receiver IN (:user_id,:other_id) ";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('other_id',$other_id);
	try{$sth->execute();}
	catch(Exception $e){}
	
	return true;
	}
	
	
	public static function PushCheck($user_id){
	global $conn;
		
	$sql="select * from user_setting where user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
	
	public static function getLikeStatus($user_id,$other_id){
	global $conn;
	
	$sql="select * from user_like where liked_by=:other_id and liked_to=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('other_id',$other_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$likes=$sth->fetchAll();
	
	$like_status=$likes[0]['status']?$likes[0]['status']:"0";
	
	return $like_status;
	}
	
	public static function get_push_ids($user_id){
	global $conn;
	
	$sql="select users.* from users where users.id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
		
	return $result;
	}
	
	public static function getFriendsId($user_id){
			
	global $conn;

	$sql = "Select user_id,friend_fbid as fbid,name,fb_image as profile_pic from facebook_friends WHERE user_id =:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
	public static function check_match($user_id){
	
	global $conn;
	
		$path=BASE_PATH."timthumb.php?src=uploads/";
	$sql="SELECT users.id as user_id,users.fbid,users.username as name,concat('$path',users.profile_pic) as profile_pic,users.profile_pic as profile_pic_name, users.bio,users.age,users.city, users.age as age_preference,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited,
	user_detail.height as height_preference,user_detail.employment_place,sexual_pref.s_preference as sexual_preference,education.education_level as education_preference,profession.profession_status as prefessional_preference, relation.rel_status as relationship_preference, food_pref.f_preference as food_preference,
	drinking.drinking_status as drinking_preference,smoking.smoking_status as smoking_preference,religion.religion_status as religion_preference, 
	(SELECT count(blocked_users.id) from blocked_users WHERE blocked_users.block_by=:user_id and blocked_users.block_to=users.id) as is_blocked,
	dating_pref.d_preference as dating_preference FROM `user_like` 
	left join user_detail on user_detail.user_id=user_like.liked_to 
	left join education on education.id=user_detail.education_id 
	left join profession on profession.id=user_detail.profession_id 
	left join relation on relation.id=user_detail.relation_id 
	left join food_pref on food_pref.id=user_detail.food_pref_id 
	left join drinking on drinking.id=user_detail.drinking_id 
	left join smoking on smoking.id=user_detail.smoking_id 
	join users on users.id=user_detail.user_id 
	LEFT JOIN religion on religion.id=user_detail.religion_id 
	LEFT JOIN dating_pref on dating_pref.id=user_detail.dating_pref_id 
	left join sexual_pref on sexual_pref.id=user_detail.sexual_pref_id 
	WHERE liked_by=:user_id and user_like.status=1 and liked_to IN (select liked_by from user_like where liked_to=:user_id and user_like.status=1) ORDER BY users.id DESC";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);	
	
	return $result;
	}
	
	//if I have unliked a user
	public static function check_rejected($user_id){
	
	global $conn;
	$sql="SELECT * FROM `user_like` WHERE liked_by=:user_id and user_like.status=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);	
	
	return $result;
	}
	
	//select * from facebook_friends where friend_fbid IN (select friend_fbid from facebook_friends where user_id=1) and user_id!=1
	public static function getMutualFriendsList($my_id,$user_id2){
	
	global $conn;	
	$sql="select user_id,friend_fbid as fbid,name,fb_image as profile_pic from facebook_friends WHERE friend_fbid IN (select friend_fbid from facebook_friends where user_id=:user_id1) and user_id!=:user_id1 and user_id=:user_id2";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id1',$my_id);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
	public static function getAllFriendsCount($user_id){
	
	global $conn;	
	$sql="select count(*) as count from facebook_friends WHERE friend_fbid IN (select friend_fbid from facebook_friends where user_id=:user_id)";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result[0]['count'];
	}
	
	public static function generateRandomString($length = 10){
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++){
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
}
?>

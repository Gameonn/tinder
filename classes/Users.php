<?php
class Users{

	public static function getUserId($token){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('token',$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	
	return $user_id;
	}
	
	public static function getUserfbId($token){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('token',$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_fbid=$result[0]['fbid'];
	
	return $user_fbid;
	}
	
		
	public static function checkfb($fbid){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_fbid=$result[0]['fbid'];
	
	return $user_fbid;
	}
	
	public static function checkuserid($fbid){
		
	global $conn;
	$data=array();
	$sql="select * from users where users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	
	return $user_id;	
		
	}
	
	public static function getBlockIds($user_id){
	
		global $conn;
	    $sql="SELECT blocked_users.block_to FROM blocked_users WHERE blocked_users.block_by=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row){
		  $userids[] = $row["block_to"];
		}
	
	return $userids;//blocked ones
	}
	
	public static function getLatLong($user_id){
	
	global $conn;
	$sql="SELECT lat,lang from users where users.id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
	public static function getUserDistance($user_id){
	
	global $conn;
	$sql="SELECT distance from user_setting where user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	$distance=$result[0]['distance']?$result[0]['distance']:2000;
	
	return $distance;
	}
	
	
	public static function getCheckParameter($user_id){
	
	global $conn;
	$sql="SELECT * from user_setting WHERE user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	$check_parameter=$result[0]['check_parameter']?$result[0]['check_parameter']:0;
	
	return $check_parameter;
	}
	
	public static function getUserLookingPref($user_id){
	
	global $conn;
	$sql="select interested_in from user_setting where user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	$gender=$result[0]['interested_in']?$result[0]['interested_in']:'Women';
	return $gender;
	}
	
	public static function getGalleryCount($user_id){
		
		global $conn;
		$sql="SELECT count(gallery.id) as gallery_count from gallery WHERE gallery.user_id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
		$gallery_count=$result[0]['gallery_count'];
	
	return $gallery_count;
		
	}
	
	
	public static function getFavorites($user_id){
	
	global $conn;
	
	$user_latlong=self::getLatLong($user_id);
	$lat=$user_latlong[0]['lat']?$user_latlong[0]['lat']:'30.75';
	$lang=$user_latlong[0]['lang']?$user_latlong[0]['lang']:'76.78';
	
	$path=BASE_PATH."timthumb.php?src=uploads/";
	$path1=BASE_PATH."uploads/";
		
	$sql="SELECT (SELECT count(user_like.id) FROM `user_like` where liked_by=:user_id and liked_to IN (select liked_by from user_like where liked_to=:user_id and liked_by=users.id and user_like.status=1) and user_like.status=1) as is_match,(select count(user_like.id) from user_like where liked_by=users.id and liked_to=:user_id and status=1) as user_like_status,
	users.id as user_id,users.fbid,users.username as name,users.id as user_id,users.gender,users.bio,users.age, users.city,
	CONCAT(TRUNCATE(( 3961 * acos( cos( radians( '$lat' ) ) * cos( radians( users.lat ) ) * cos( radians( users.lang ) - radians( '$lang' ) ) + sin( radians( '$lat' ) ) * sin( radians( users.lat ) ) ) ),0),' mi away') AS distance,
	(SELECT ifnull((SELECT group_concat(concat('$path1',gallery.image) SEPARATOR ',') as image	FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery,
		(SELECT ifnull((SELECT group_concat(gallery.image SEPARATOR ',') as image FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery_image_names,
	CONCAT('$path',users.profile_pic) as profile_pic,users.profile_pic as profile_pic_name, (select count(favorite.id) from favorite where fav_by=:user_id and fav_to=users.id) as is_favorite 
	FROM `favorite` JOIN users on users.id=favorite.fav_to where favorite.fav_by=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
	
	public static function getPhotosJson($fb_token,$fbid){
	
	global $conn;
	$params = array('access_token' => $fb_token);
        $url = "https://graph.facebook.com/".$fbid."/photos";
        $url .= '?' . http_build_query($params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_USERAGENT ,'');
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
	
	return $output;
	}	
	
	public static function getAlbumsJson($fb_token,$fbid){
	
	global $conn;
	$params = array('access_token' => $fb_token);
        $url = "https://graph.facebook.com/".$fbid."/albums";
        $url .= '?' . http_build_query($params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_USERAGENT ,'');
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
	
	return $output;
	}	
	
	
	public static function AddFriendsLogin($uid,$friend){
	
	global $conn;
	
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
		
	return true;
	}
	
	public static function AddPhotos($user_id,$image,$fb_token){
	
		global $conn;
		
		$output = json_decode($image,true);
		$gallery_count=Users::getGalleryCount($user_id);
		
		$q=6-$gallery_count;
		
		if($q<6 && $q!=0){
			
		//foreach($output['data'] as $k=>$row){
		for($k=0; $k<$q; $k++){		
	    $sql="SELECT * FROM gallery WHERE gallery.user_id=:user_id and gallery.fb_photo_id=:photo_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('photo_id',$output['data'][$k]['id']);
		try{$sth->execute();}
		catch(Exception $e){}
		$result[$k]=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(!$result[$k]){
		
		$fb_pic = file_get_contents('https://graph.facebook.com/'.$output['data'][$k]['id'].'/picture?access_token='.$fb_token);
		$fb_image[$k] = 'IMG_'.$output['data'][$k]['id'].'.jpg';
		file_put_contents("../uploads/".$fb_image[$k], $fb_pic);
		
		//$fb_image[$k]='https://graph.facebook.com/'.$row["id"].'/picture?access_token='.$fb_token;
		
		$sql="INSERT INTO gallery(id,user_id,fb_photo_id,image,is_profile_pic,created_on) VALUES(DEFAULT,:user_id,:fb_photo_id,:image,0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('fb_photo_id',$output['data'][$k]['id']);
		$sth->bindValue('image',$fb_image[$k]);
		try{$sth->execute();}
		catch(Exception $e){}
		}
		}
		}
		
		return true;
	}
	
	public static function getInterestJson($fb_token,$fbid){
	
	global $conn;
	$params = array('access_token' => $fb_token);
        $url = "https://graph.facebook.com/".$fbid."/likes";
        $url .= '?' . http_build_query($params);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_USERAGENT ,'');
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
	
	return $output;
	}
	

	public static function AddCommonInterests($user_id,$interests){
	
		global $conn;
		$output = json_decode($interests,true);
  
		foreach($output['data'] as $k=>$row){
				
	    $sql="SELECT * FROM interests WHERE interests.user_id=:user_id and interests.interest=:interest";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('interest',$row['name']);
		try{$sth->execute();}
		catch(Exception $e){}
		$result[$k]=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(!$result[$k]){
		$sql="INSERT INTO interests(id,user_id,interest,created_on) VALUES(DEFAULT,:user_id,:interest,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('interest',$row['name']);
		try{$sth->execute();}
		catch(Exception $e){}
		}
		}
		
		return true;
	}
	
	public static function getFriendsJson($fb_token,$fbid){
	
	global $conn;
	$params = array('access_token' => $fb_token);
        $url = "https://graph.facebook.com/me/friends";
        $url .= '?' . http_build_query($params);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_USERAGENT ,'');
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
	
	return $output;
	}
	

	public static function AddFriends($user_id,$friends){
	
		global $conn;
		$output = json_decode($friends,true);
		
		if($output){
		foreach($output['data'] as $k=>$row){
				
	    $sql="SELECT * FROM facebook_friends WHERE facebook_friends.user_id=:user_id and facebook_friends.friend_fbid=:fbid";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('fbid',$row['id']);
		try{$sth->execute();}
		catch(Exception $e){}
		$result[$k]=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		$fb_image='https://graph.facebook.com/'.$row["id"].'/picture';
		
		if(!$result[$k]){
		$sql="INSERT INTO facebook_friends(id,user_id,friend_fbid,name,fb_image,created_on) VALUES(DEFAULT,:user_id,:friend_fbid,:name,:fb_image,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('name',$row['name']);
		$sth->bindValue('friend_fbid',$row['id']);
		$sth->bindValue('fb_image',$fb_image);
		try{$sth->execute();}
		catch(Exception $e){}
		}
		}
		}
		
		return true;
	}
	
	
	public static function getPartnerPref($user_id){
	
		global $conn;
	    $sql="SELECT partner_pref.user_id,partner_pref.min_age,partner_pref.max_age,partner_pref.height as p_height_preference,education.education_level as p_education_preference,profession.profession_status as p_profession_preference,
		relation.rel_status as p_relationship_preference,sexual_pref.s_preference as p_sexual_preference, food_pref.f_preference as p_food_preference, drinking.drinking_status as p_drinking_preference,
		smoking.smoking_status as p_smoking_preference,religion.religion_status as p_religion_preference,dating_pref.d_preference as p_dating_preference FROM partner_pref 
		LEFT JOIN education on education.id=partner_pref.education_id left join profession on profession.id=partner_pref.profession_id 
		LEFT JOIN relation on relation.id=partner_pref.relation_id left join food_pref on food_pref.id=partner_pref.food_pref_id 
		LEFT JOIN drinking on drinking.id=partner_pref.drinking_id left join smoking on smoking.id=partner_pref.smoking_id LEFT JOIN sexual_pref on sexual_pref.id=partner_pref.sexual_pref_id
		LEFT JOIN religion on religion.id=partner_pref.religion_id LEFT JOIN dating_pref on dating_pref.id=partner_pref.dating_pref_id WHERE user_id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		$partner_pref=$result['0'];
	
	return $partner_pref;//partner preference array
	}
	
		//used in search algorithm
		public static function getPartnerPrefForSearch($user_id){
	
		global $conn;
	    $sql="SELECT * FROM partner_pref WHERE user_id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		$partner_pref=$result['0'];
	
	return $partner_pref;//partner preference array
	}
	
	
	/*
	//using block ids
	$blockIds = self::getBlockIds($loginuserid);
			if($blockIds){
			$Where .=  " AND id NOT IN (".implode(",",$blockIds).") ";
			}
	*/
	
	public static function getUserProfile($user_id){
		global $conn;
		$profile_info='';
		$path=BASE_PATH."timthumb.php?src=uploads/";
			$path1=BASE_PATH."uploads/";
		$sql="SELECT users.id as user_id,fbid,username as name,email,gender, bio, age, city, lat, lang, concat('$path',profile_pic) as profile_pic,users.profile_pic as profile_pic_name,ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id and interests.interest IN (select interests.interest from interests where interests.user_id=:user_id )),'') as  interest,'0 mi away' as distance,users.age as age_preference,partner_pref.user_id,partner_pref.min_age, partner_pref.max_age,partner_pref.height as height_preference, education.education_level as education_preference, profession.profession_status as profession_preference, relation.rel_status as relationship_preference, food_pref.f_preference as food_preference, sexual_pref.s_preference as sexual_preference, drinking.drinking_status as drinking_preference,smoking.smoking_status as smoking_preference,religion.religion_status as religion_preference, dating_pref.d_preference as dating_preference,
		(SELECT ifnull((SELECT group_concat(concat('$path1',gallery.image) SEPARATOR ',') as image FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery,
		(SELECT ifnull((SELECT group_concat(gallery.image SEPARATOR ',') as image FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery_image_names,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited
		FROM users LEFT JOIN partner_pref on partner_pref.user_id=users.id left join education on education.id=partner_pref.education_id left join profession on profession.id=partner_pref.profession_id left join relation on relation.id=partner_pref.relation_id left join food_pref on food_pref.id=partner_pref.food_pref_id left join sexual_pref on sexual_pref.id=partner_pref.sexual_pref_id left join drinking on drinking.id=partner_pref.drinking_id left join smoking on smoking.id=partner_pref.smoking_id left JOIN religion on religion.id=partner_pref.religion_id LEFT JOIN dating_pref on dating_pref.id=partner_pref.dating_pref_id  WHERE users.id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
			$profile_info=$result['0'];
		}
		catch(Exception $e){}
		return $profile_info;
	}
	
	
		public static function getOtherProfile1($myid,$user_id){
		global $conn;
		$profile_info='';
		$path=BASE_PATH."timthumb.php?src=uploads/";
		$sql="SELECT id,fbid,username as name,email,gender, bio, age, city, lat, lang, concat('$path',profile_pic) as profile_pic,ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id and interests.interest IN (select interests.interest from interests where interests.user_id=:myid )),'') as  common_interest,(select count(favorite.id) from favorite where favorite.fav_by=:myid and fav_to=:user_id) as is_favorite,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited
		FROM users WHERE users.id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('myid',$myid);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
			$profile_info=$result['0'];
		}
		catch(Exception $e){}
		return $profile_info;
	}
	
			public static function getOtherProfile($myid,$user_id,$lat,$lang){
		global $conn;
		$profile_info='';
		$path=BASE_PATH."timthumb.php?src=uploads/";
		$path1=BASE_PATH."uploads/";
		
		$sql="SELECT users.id as user_id,fbid,username as name,email,gender, bio, age, city, lat, lang, concat('$path',profile_pic) as profile_pic,users.profile_pic as profile_pic_name,
		ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id ),'') as  interest,(select count(favorite.id) from favorite where favorite.fav_by=:myid and fav_to=:user_id) as is_favorite,
		users.age as age_preference,user_detail.height,user_detail.height as height_preference,education.education_level as education_preference, profession.profession_status as profession_preference, relation.rel_status as relationship_preference, food_pref.f_preference as food_preference, sexual_pref.s_preference as sexual_preference,drinking.drinking_status as drinking_preference ,smoking.smoking_status as smoking_preference,religion.religion_status as religion_preference,dating_pref.d_preference as dating_preference, user_detail.employment_place,
		(SELECT ifnull((SELECT group_concat(concat('$path1',gallery.image) SEPARATOR ',') as image	FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery,
		(SELECT ifnull((SELECT group_concat(gallery.image SEPARATOR ',') as image FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery_image_names,	
		(SELECT count(user_like.id) from user_like where liked_by=users.id and liked_to=:myid and status=1) as user_like_status,		
		CONCAT(TRUNCATE(( 3961 * acos( cos( radians( '$lat' ) ) * cos( radians( users.lat ) ) * cos( radians( users.lang ) - radians( '$lang' ) ) + sin( radians( '$lat' ) ) * sin( radians( users.lat ) ) ) ),0),' mi away') AS distance,
		(SELECT count(user_like.id) FROM `user_like` where liked_by=:myid and liked_to IN (select liked_by from user_like where liked_to=:myid and liked_by=:user_id and user_like.status=1) and user_like.status=1) as is_match,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited
		FROM users LEFT JOIN user_detail on user_detail.user_id=users.id LEFT JOIN education on education.id=user_detail.education_id LEFT JOIN profession on profession.id=user_detail.profession_id LEFT JOIN relation on relation.id=user_detail.relation_id LEFT JOIN food_pref on food_pref.id=user_detail.food_pref_id LEFT JOIN sexual_pref on sexual_pref.id=user_detail.sexual_pref_id LEFT JOIN drinking on drinking.id=user_detail.drinking_id LEFT JOIN smoking on smoking.id=user_detail.smoking_id LEFT JOIN religion on religion.id=user_detail.religion_id LEFT JOIN dating_pref on dating_pref.id=user_detail.dating_pref_id  WHERE users.id=:user_id";
		
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('myid',$myid);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
			$profile_info=$result['0'];
		}
		catch(Exception $e){}
		return $profile_info;
	}
	
	public static function getUserGallery($user_id){
		global $conn;
		$profile_info='';
		$path=BASE_PATH."uploads/";
		$sql="SELECT gallery.id as gallery_id,gallery.user_id,concat('$path',gallery.image) as image,	
			CASE 
                  WHEN DATEDIFF(NOW(),gallery.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),gallery.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),gallery.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),gallery.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),gallery.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),gallery.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),gallery.created_on)) ,' s ago')
                END as time_elapsed
			FROM gallery WHERE user_id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue(':user_id',$user_id);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(Exception $e){}
		return $result;
	}
	
	public static function getGallery($user_id){
		global $conn;
		$profile_info='';
		$path=BASE_PATH."/uploads/";
		$sql="SELECT group_concat(concat('$path',gallery.image) SEPARATOR ',') as image	FROM gallery WHERE user_id=:user_id ORDER BY is_profile_pic DESC";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(Exception $e){}
		return $result;
	}
	
	public static function UpdateLastVisitedTime($user_id){
	
	global $conn;
	$sql="UPDATE users set created_on=NOW(),last_visited=NOW() where id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	return true;
	}
	
	public static function UpdateLatLang($user_id,$lat,$lang){
	
	global $conn;
	$sql="UPDATE users set lat=:lat,lang=:lang where id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('lat',$lat);
	$sth->bindValue('lang',$lang);
	try{$sth->execute();}
	catch(Exception $e){}
	return true;
	}
	
	public static function savePartnerPreferences($user_id,$min_age,$max_age,$education_id,$profession_id,$relation_id,$food_pref_id,$drinking_id,$smoking_id,$religion_id,$dating_pref_id,$sexual_pref_id,$height){
		global $conn;
		
		$sql = "INSERT INTO partner_pref(id,user_id,min_age,max_age,education_id,profession_id,sexual_pref_id,height, relation_id,food_pref_id,drinking_id, smoking_id, religion_id, dating_pref_id,created_on) VALUES(DEFAULT,:user_id,:min_age,:max_age,:education_id,:profession_id,:sexual_pref_id,:height,:relation_id,:food_pref_id,:drinking_id,:smoking_id,:religion_id, :dating_pref_id, NOW())";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('min_age',$min_age);
		$sth->bindValue('max_age',$max_age);
		$sth->bindValue('education_id',$education_id);
		$sth->bindValue('profession_id',$profession_id);
		$sth->bindValue('sexual_pref_id',$sexual_pref_id);
		$sth->bindValue('height',$height);
		$sth->bindValue('relation_id',$relation_id);
		$sth->bindValue('food_pref_id',$food_pref_id);
		$sth->bindValue('drinking_id',$drinking_id);
		$sth->bindValue('smoking_id',$smoking_id);
		$sth->bindValue('religion_id',$religion_id);
		$sth->bindValue('dating_pref_id',$dating_pref_id);
		try{$sth->execute();}
		catch(Exception $e){}
		
		return true;
	}
	
		public static function UpdatePartnerPreferences($user_id,$min_age,$max_age,$education_id,$profession_id,$relation_id,$food_pref_id,$drinking_id,$smoking_id,$religion_id,$dating_pref_id,$sexual_pref_id,$height){
		global $conn;
		
		$sql = "UPDATE partner_pref set min_age=:min_age,max_age=:max_age,education_id=:education_id,profession_id=:profession_id, sexual_pref_id=:sexual_pref_id,height=:height,relation_id=:relation_id,food_pref_id=:food_pref_id,drinking_id=:drinking_id, smoking_id=:smoking_id, religion_id=:religion_id, dating_pref_id=:dating_pref_id where user_id=:user_id";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('min_age',$min_age);
		$sth->bindValue('max_age',$max_age);
		$sth->bindValue('education_id',$education_id);
		$sth->bindValue('profession_id',$profession_id);
		$sth->bindValue('sexual_pref_id',$sexual_pref_id);
		$sth->bindValue('height',$height);
		$sth->bindValue('relation_id',$relation_id);
		$sth->bindValue('food_pref_id',$food_pref_id);
		$sth->bindValue('drinking_id',$drinking_id);
		$sth->bindValue('smoking_id',$smoking_id);
		$sth->bindValue('religion_id',$religion_id);
		$sth->bindValue('dating_pref_id',$dating_pref_id);
		try{$sth->execute();}
		catch(Exception $e){}
		
		return true;
	}
	
		public static function UpdateUserDetail($user_id,$education_id,$profession_id,$relation_id,$food_pref_id,$drinking_id,$smoking_id,$religion_id,$dating_pref_id,$sexual_pref_id,$height,$employment_place){
		global $conn;
		
		$sql = "UPDATE user_detail set education_id=:education_id,profession_id=:profession_id, sexual_pref_id=:sexual_pref_id,height=:height,relation_id=:relation_id,food_pref_id=:food_pref_id,drinking_id=:drinking_id, smoking_id=:smoking_id, religion_id=:religion_id, dating_pref_id=:dating_pref_id,employment_place=:employment_place where user_id=:user_id";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('education_id',$education_id);
		$sth->bindValue('profession_id',$profession_id);
		$sth->bindValue('sexual_pref_id',$sexual_pref_id);
		$sth->bindValue('height',$height);
		$sth->bindValue('relation_id',$relation_id);
		$sth->bindValue('food_pref_id',$food_pref_id);
		$sth->bindValue('drinking_id',$drinking_id);
		$sth->bindValue('smoking_id',$smoking_id);
		$sth->bindValue('religion_id',$religion_id);
		$sth->bindValue('dating_pref_id',$dating_pref_id);
		$sth->bindValue('employment_place',$employment_place);
		try{$sth->execute();}
		catch(Exception $e){}
		
	
	}
	
	public static function saveImageToGallery($user_id,$image_path){
		global $conn;
		
		$fb_photo_id=Users::generateRandomImgId();
		
		$sql = "INSERT INTO gallery(id,user_id,fb_photo_id,image,is_profile_pic,created_on) VALUES(DEFAULT,:user_id,:fb_photo_id,:image,0,NOW())";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('fb_photo_id',$fb_photo_id);
		$sth->bindValue('image',$image_path);
		try{$sth->execute();}
		catch(Exception $e){
		echo $e->getMessage();
		}
		
		return true;
	}
	
	public static function getCommonInterests($myid,$other_id){
	
	global $conn;
	$sql="SELECT group_concat(interest SEPARATOR ',') as common_interests from interests where interests.user_id=:other_id and interests.interest IN (select interests.interest from interests where interests.user_id=:myid )";
	$sth=$conn->prepare($sql);
	$sth->bindValue('myid',$myid);
	$sth->bindValue('other_id',$other_id);
	try{
		$sth->execute();
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);	
		}
	catch(Exception $e){}
	
	$interests=$result[0]['common_interests']?$result[0]['common_interests']:"";
	
	return $interests;
	}
	
	//used in search api for fetching count of common_interests
	public static function getCommonInterestsSearch($myid,$other_id){
	
	global $conn;
	$sql="SELECT interest from interests where interests.user_id=:other_id and interests.interest IN (select interests.interest from interests where interests.user_id=:myid )";
	$sth=$conn->prepare($sql);
	$sth->bindValue('myid',$myid);
	$sth->bindValue('other_id',$other_id);
	try{
		$sth->execute();
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);	
		}
	catch(Exception $e){}
	
	
	//$interests=$result[0]['common_interests']?$result[0]['common_interests']:"";
	
	return $result;
	}
	
	
	public static function fbsignin($fbid){
	
	global $conn;
	$data=array();
	$path=BASE_PATH."uploads/";
	$uid=self::checkuserid($fbid);	
		
	$sql="SELECT users.*,ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id and 
	interests.interest IN (select interests.interest from interests where interests.user_id=users.id )),'') as  interest,'0 mi away' as distance,
	users.age ,partner_pref.user_id,partner_pref.min_age, partner_pref.max_age, education.education_level , profession.profession_status , relation.rel_status , food_pref.f_preference , user_detail.height ,user_detail.employment_place, sexual_pref.s_preference ,drinking.drinking_status ,smoking.smoking_status , religion.religion_status , dating_pref.d_preference ,
	(SELECT group_concat(concat('$path',temp.image) SEPARATOR ',') as image FROM (SELECT gallery.image,gallery.is_profile_pic from gallery WHERE gallery.user_id=:user_id ORDER BY gallery.is_profile_pic DESC) as temp) as gallery,	
	(SELECT ifnull((SELECT group_concat(temp.image SEPARATOR ',') as image FROM (SELECT gallery.image,gallery.is_profile_pic from gallery WHERE gallery.user_id=:user_id ORDER BY gallery.is_profile_pic DESC ) as temp),'')) as gallery_image_names,
		  user_setting.discovery as discovery_preference,user_setting.distance as distance_preference,user_setting.interested_in as looking_preference,
		  user_setting.new_matches,user_setting.push_notification,user_setting.check_parameter,user_setting.profile_parameter,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited
		FROM users LEFT JOIN partner_pref on partner_pref.user_id=users.id
		LEFT JOIN user_detail on user_detail.user_id=users.id
		LEFT JOIN user_setting on user_setting.user_id=users.id left join education on education.id=user_detail.education_id LEFT JOIN profession on profession.id=user_detail.profession_id left join relation on relation.id=user_detail.relation_id left join food_pref on food_pref.id=user_detail.food_pref_id left join sexual_pref on sexual_pref.id=user_detail.sexual_pref_id left join drinking on drinking.id=user_detail.drinking_id left join smoking on smoking.id=user_detail.smoking_id left JOIN religion on religion.id=user_detail.religion_id LEFT JOIN dating_pref on dating_pref.id=user_detail.dating_pref_id  WHERE users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	$token=Users::generateRandomString(12);
	
	$sql="UPDATE users set token=:token where users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	$sth->bindValue('token',md5($token));
	try{$sth->execute();}
	catch(Exception $e){}
	
		$data=array(
	        "user_id"=>$result[0]['id'],
	        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
	        "name"=>$result[0]['username']?$result[0]['username']:"",
	        "email"=>$result[0]['email']?$result[0]['email']:"",
	        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
	        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
			"interest"=>$result[0]['interest']?$result[0]['interest']:"",
			"bio"=>$result[0]['bio']?$result[0]['bio']:"",
			"age"=>$result[0]['age']?$result[0]['age']:"0",
			"age_preference"=>$result[0]['age']?$result[0]['age']:"0",
			"city"=>$result[0]['city']?$result[0]['city']:"",
			"lat"=>$result[0]['lat']?$result[0]['lat']:"",
			"lang"=>$result[0]['lang']?$result[0]['lang']:"",
			"min_age"=> $result[0]['min_age']?$result[0]['min_age']:"",
			"max_age"=> $result[0]['max_age']?$result[0]['max_age']:"",
			"education_preference"=> $result[0]['education_level']?$result[0]['education_level']:"",
			"profession_preference"=> $result[0]['profession_status']?$result[0]['profession_status']:"",
			"relationship_preference"=> $result[0]['rel_status']?$result[0]['rel_status']:"",
			"food_preference"=>$result[0]['f_preference']?$result[0]['f_preference']:"",
			"drinking_preference"=> $result[0]['drinking_status']?$result[0]['drinking_status']:"",
			"new_matches"=> $result[0]['new_matches']?$result[0]['new_matches']:"0",
			"push_notification"=> $result[0]['push_notification']?$result[0]['push_notification']:"0",
			"smoking_preference"=> $result[0]['smoking_status']?$result[0]['smoking_status']:"",
			"religion_preference"=> $result[0]['religion_status']?$result[0]['religion_status']:"",
			"dating_preference"=> $result[0]['d_preference']?$result[0]['d_preference']:"",
			"sexual_preference"=> $result[0]['s_preference']?$result[0]['s_preference']:"",
			"height_preference"=> $result[0]['height']?$result[0]['height']:"",
			"employment_place"=> $result[0]['employment_place']?$result[0]['employment_place']:"",
			"discovery_preference"=> $result[0]['discovery_preference']?$result[0]['discovery_preference']:"0",
			"distance_preference"=> $result[0]['distance_preference']?$result[0]['distance_preference']:"0",
			"looking_preference"=> $result[0]['looking_preference']?$result[0]['looking_preference']:"0",
			"check_parameter"=> $result[0]['check_parameter']?$result[0]['check_parameter']:"0",
			"profile_parameter"=> $result[0]['profile_parameter']?$result[0]['profile_parameter']:"0",
			"distance"=> $result[0]['distance']?$result[0]['distance']:"0 mi away",
			"last_visited"=> $result[0]['last_visited']?$result[0]['last_visited']:"",
	        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
	        "profile_pic_name"=>$result[0]['profile_pic']?$result[0]['profile_pic']:"no_image.png",
	        "access_token"=>md5($token),
			"gallery"=>$result[0]['gallery']?$result[0]['gallery']:"",
			"gallery_image_names"=>$result[0]['gallery_image_names']?$result[0]['gallery_image_names']:""
        );
		
	return $data;
	}
	
	
	public static function fbsigninNew($fbid){
	
	global $conn;
	$data=array();
	$path=BASE_PATH."uploads/";
	$path1=BASE_PATH."/timthumb.php?src=uploads/";
	
	$uid=self::checkuserid($fbid);	
	
	$sql="SELECT users.id as user_id,fbid,username as name,token as access_token,email,gender, bio, age, city, lat, lang, concat('$path1',profile_pic) as profile_pic,users.profile_pic as profile_pic_name,
		ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id ),'') as  interest, 0 as is_favorite,
		partner_pref.user_id,partner_pref.min_age,partner_pref.max_age,users.age as age_preference,user_detail.height as height_preference,user_detail.employment_place,
		education.education_level as education_preference, profession.profession_status as profession_preference, relation.rel_status as relationship_preference, 
		food_pref.f_preference as food_preference, sexual_pref.s_preference as sexual_preference,drinking.drinking_status as drinking_preference ,
		smoking.smoking_status as smoking_preference,religion.religion_status as religion_preference,dating_pref.d_preference as dating_preference,
		(SELECT ifnull((SELECT group_concat(temp.image SEPARATOR ',') as image FROM (SELECT gallery.image,gallery.is_profile_pic from gallery WHERE gallery.user_id=:user_id ORDER BY gallery.is_profile_pic DESC ) as temp),'')) as gallery_image_names,
		(SELECT ifnull((SELECT group_concat(concat('$path',temp.image) SEPARATOR ',') as image FROM (SELECT gallery.image,gallery.is_profile_pic from gallery WHERE gallery.user_id=:user_id ORDER BY gallery.is_profile_pic DESC ) as temp),'')) as gallery,
		'0 mi away' AS distance,user_setting.new_matches,user_setting.push_notification,
		user_setting.discovery as discovery_preference,user_setting.distance as distance_preference,user_setting.interested_in as looking_preference,user_setting.check_parameter,
		user_setting.profile_parameter,
		0 as is_match,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited
		FROM users LEFT JOIN partner_pref on partner_pref.user_id=users.id
		LEFT JOIN user_detail on user_detail.user_id=users.id 
		LEFT JOIN education on education.id=user_detail.education_id 
		LEFT JOIN user_setting on user_setting.user_id=users.id 
		LEFT JOIN profession on profession.id=user_detail.profession_id LEFT JOIN relation on relation.id=user_detail.relation_id 
		LEFT JOIN food_pref on food_pref.id=user_detail.food_pref_id LEFT JOIN sexual_pref on sexual_pref.id=user_detail.sexual_pref_id 
		LEFT JOIN drinking on drinking.id=user_detail.drinking_id LEFT JOIN smoking on smoking.id=user_detail.smoking_id 
		LEFT JOIN religion on religion.id=user_detail.religion_id LEFT JOIN dating_pref on dating_pref.id=user_detail.dating_pref_id WHERE users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	$sth->bindValue('user_id',$uid);
	try{
		$sth->execute();
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		$profile_info=$result['0'];
	}
	catch(Exception $e){}
	return $profile_info;
	}
	
	public static function myprofile($user_id){
	
	global $conn;
	$data=array();
		$path=BASE_PATH."uploads/";
	$sql="SELECT users.id as user_id,fbid,username as name,email,gender, bio, age, city, lat, lang, concat('$path',profile_pic) as profile_pic,users.profile_pic as profile_pic_name,
		ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id ),'') as  interest,	0 as is_favorite,
		partner_pref.user_id,partner_pref.min_age,partner_pref.max_age,users.age as age_preference,partner_pref.height as height_preference,
		education.education_level as education_preference, profession.profession_status as profession_preference, relation.rel_status as relationship_preference, 
		food_pref.f_preference as food_preference, sexual_pref.s_preference as sexual_preference,drinking.drinking_status as drinking_preference ,
		smoking.smoking_status as smoking_preference,religion.religion_status as religion_preference,dating_pref.d_preference as dating_preference,
		(SELECT ifnull((SELECT group_concat(gallery.image SEPARATOR ',') as image FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery_image_names,		
		(SELECT ifnull((SELECT group_concat(concat('$path1',gallery.image) SEPARATOR ',') as image	FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery,
		'0 mi away' AS distance,
		0 as is_match,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited
		FROM users LEFT JOIN partner_pref on partner_pref.user_id=users.id LEFT JOIN education on education.id=partner_pref.education_id LEFT JOIN profession on profession.id=partner_pref.profession_id LEFT JOIN relation on relation.id=partner_pref.relation_id LEFT JOIN food_pref on food_pref.id=partner_pref.food_pref_id LEFT JOIN sexual_pref on sexual_pref.id=partner_pref.sexual_pref_id LEFT JOIN drinking on drinking.id=partner_pref.drinking_id LEFT JOIN smoking on smoking.id=partner_pref.smoking_id LEFT JOIN religion on religion.id=partner_pref.religion_id LEFT JOIN dating_pref on dating_pref.id=partner_pref.dating_pref_id  WHERE users.id=:user_id
";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{
		$sth->execute();
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		$profile_info=$result['0'];
	}
	catch(Exception $e){}
	return $profile_info;
	}
	
	public static function getCurrentPic($user_id){
		
	global $conn;
	
		$sql="select profile_pic from users WHERE users.id=:user_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll();
		
		$profile_pic=$result[0]['profile_pic'];
		return $profile_pic;
	}
	
	
	public static function getPicId($pic_name){
		
	global $conn;
	
		$sql="select id from gallery WHERE gallery.image=:pic_name";
		$sth=$conn->prepare($sql);
		$sth->bindValue('pic_name',$pic_name);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll();
		
		$profile_pic_id=$result[0]['id'];
		return $profile_pic_id;
	}
	
	
	//calculation of post created time
	public static function time_since($created_on){
	global $conn;
	$sth=$conn->prepare("select UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP('$created_on') as time_diff");
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	$diff=$res[0]['time_diff'];

	if($diff < 60){
		$response = $diff.' s ago';
	}elseif($diff < 3600){
		$response = floor($diff / 60).' mins ago';	
	}elseif($diff < 86400){
		$response = floor($diff / 3600).' hrs ago';
	}elseif($diff < 2592000){
		$response = floor($diff / 86400).' days ago';
	}elseif($diff < 31104000){
		$response = floor($diff / 2592000).' months ago';
	}else{
		$response = floor($diff / 31104000).' year ago';
	}
	
	return $response;
	}
	
	public static function generateRandomString($length = 10){
	$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
	$randomString = '';
	for ($i = 0; $i < $length; $i++){
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
	}
	
	
	public static function generateRandomImgId($length = 15){
	
	$characters   = '0123456789';
	
	$randomString = '';
	for ($i = 0; $i < $length; $i++){
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
	}

	


}
?>

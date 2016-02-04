<?php
require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$distance=$_REQUEST['distance']?$_REQUEST['distance']:'8000';
//$lat1=$_REQUEST['lat']?$_REQUEST['lat']:'30.75';
//$lang1=$_REQUEST['lang']?$_REQUEST['lang']:'76.78';
$lat1=$_REQUEST['lat'];
$lang1=$_REQUEST['lang'];

$search_result=array();
 
if(!($token)){
$success='0';
$msg="Incomplete Parameters";
}
else{
	
	$user_id=Users::getUserId($token);
	if($user_id){
	
		if($lat1 && $lang1){
		$lat=$lat1;
		$lang=$lang1;
		$updated= Users::UpdateLatlang($user_id,$lat,$lang);
		}
		else{
		$user_latlong=Users::getLatLong($user_id);
		$lat=$user_latlong[0]['lat'];
		$lang=$user_latlong[0]['lang'];
		}
	
	$gender=Users::getUserLookingPref($user_id);
	$check_parameter=Users::getCheckParameter($user_id);
	$distance=Users::getUserDistance($user_id);
	Users::UpdateLastVisitedTime($user_id);

	//gender here refers to looking preference
	if($gender=='Men'){
	$gender_check='male';
	$looking_pref_condition="AND users.gender='male'";
	}
	elseif($gender=='Women'){
	$gender_check='female';
	$looking_pref_condition="AND users.gender='female'";
	}
	
	$path=BASE_PATH."timthumb.php?src=uploads/";
	$path1=BASE_PATH."uploads/";
	$sql="SELECT temp.* from ( SELECT users.id as user_id,users.fbid,users.username as name,users.gender,concat('$path',users.profile_pic) as profile_pic,users.bio, users.age, 
	user_detail.education_id,user_detail.profession_id,user_detail.sexual_pref_id,user_detail.food_pref_id,user_detail.drinking_id,user_detail.smoking_id,user_detail.relation_id,user_detail.dating_pref_id,
	users.city,users.created_on,(select count(favorite.id) from favorite where favorite.fav_by=:user_id and favorite.fav_to=users.id) as is_favorite, users.age as age_preference,user_detail.height,user_detail.employment_place,user_detail.height as height_preference,education.education_level as education_preference,profession.profession_status as profession_preference, relation.rel_status as relationship_preference, food_pref.f_preference as food_preference, drinking.drinking_status as drinking_preference,smoking.smoking_status as smoking_preference,religion.religion_status as religion_preference,dating_pref.d_preference as dating_preference,sexual_pref.s_preference as sexual_preference, ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id and interests.interest IN (select interests.interest from interests where interests.user_id=:user_id)),'') as  common_interest,(select count(user_like.id) from user_like where liked_by=users.id and liked_to=:user_id and status=1) as user_like_status,
	CONCAT(TRUNCATE(( 3961 * acos( cos( radians( '$lat' ) ) * cos( radians( users.lat ) ) * cos( radians( users.lang ) - radians( '$lang' ) ) + sin( radians( '$lat' ) ) * sin( radians( users.lat ) ) ) ),0),' mi away') AS distance,
	(SELECT ifnull((SELECT group_concat(concat('$path1',gallery.image) SEPARATOR ',') as image	FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery,
	(SELECT ifnull((SELECT group_concat(gallery.image SEPARATOR ',') as image FROM gallery WHERE gallery.user_id=users.id ORDER BY is_profile_pic DESC),'')) as gallery_image_names,
	user_setting.interested_in as looking_preference
	from users JOIN user_detail on user_detail.user_id=users.id 
	JOIN user_setting on user_setting.user_id=users.id 
	left join education on education.id=user_detail.education_id 
	left join profession on profession.id=user_detail.profession_id 
	left join relation on relation.id=user_detail.relation_id 
	left join food_pref on food_pref.id=user_detail.food_pref_id 
	left join sexual_pref on sexual_pref.id=user_detail.sexual_pref_id 
	left join drinking on drinking.id=user_detail.drinking_id 
	left join smoking on smoking.id=user_detail.smoking_id 
	LEFT JOIN religion on religion.id=user_detail.religion_id 
	LEFT JOIN dating_pref on dating_pref.id=user_detail.dating_pref_id 
	WHERE users.id!=:user_id and user_setting.discovery=1 $looking_pref_condition
	AND users.id NOT IN (SELECT liked_to FROM `user_like` WHERE liked_by=:user_id)
	AND users.id NOT IN (SELECT block_to FROM `blocked_users` WHERE block_by=:user_id)
	AND users.age BETWEEN (SELECT partner_pref.min_age from partner_pref WHERE user_id=:user_id) 
	AND (SELECT partner_pref.max_age from partner_pref WHERE user_id=:user_id) GROUP BY users.id ) as temp WHERE temp.distance<={$distance} LIMIT 0,10";
	
	$sth = $conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	//$sth->bindValue('gender_check',$gender_check);
	try{$sth->execute();}
	catch(PDOException $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as $key => $value) {
			//if($value['interested_in']=='Men' && $value['gender']=='male' || $value['interested_in']=='Women' && $value['gender']=='female'){
		
				$value['mutual_count'] = count(GeneralFunctions::getMutualFriendsList($user_id,$value['user_id']));//mutual friends count
				$value['match_count']=count(Users::getCommonInterestsSearch($user_id,$value['user_id']));//common_interest count
				$value['last_visited']=Messages::time_since($value['created_on']);//time elapsed from the last visited time
				$search_result[]=$value;
			//}
			}
		$arr_size=sizeof($result);
	
		//calculate matching//
		$partner_pref = Users::getPartnerPrefForSearch($user_id);
		
	
		if($check_parameter){
		$search_result=array();
		for($i=0;$i<$arr_size;$i++){
	
		$match[$i] = 0;
		if(($partner_pref['education_id'] == $result[$i]['education_id']) || ($partner_pref['education_id'] == 4 ))
			$match[$i]+= 1;
		if(($partner_pref['profession_id'] == $result[$i]['profession_id']) || ($partner_pref['profession_id'] == 4))
			$match[$i]+= 1;
		if(($partner_pref['realtion_id'] == $result[$i]['relation_id']) || ($partner_pref['realtion_id'] == 4))
			$match[$i]+= 1;
		if(($partner_pref['food_pref_id'] == $result[$i]['food_pref_id']) || ($partner_pref['food_pref_id'] == 4))
			$match[$i]+= 1;
		if(($partner_pref['drinking_id'] == $result[$i]['drinking_id']) || ($partner_pref['drinking_id'] == 4))
			$match[$i]+= 1;
		if(($partner_pref['smoking_id'] == $result[$i]['smoking_id']) || ($partner_pref['smoking_id'] == 4))
			$match[$i]+= 1;
		if(($partner_pref['religion_id'] == $result[$i]['religion_id']) || ($partner_pref['religion_id'] == 4))
			$match[$i]+= 1;
		if(($partner_pref['dating_pref_id'] == $result[$i]['dating_pref_id']) || ($partner_pref['dating_pref_id']==4))
			$match[$i]+= 1;
	
	
		$result[$i]['matching'] = (string)$match[$i];
			if($result[$i]['matching']>=3){
			$search_result[]=$result[$i];
			}
			
		}
		}
		
		
		
		if($result){
		$success='1';
		$msg='success';
		}
		else{
		$success='0';
		$msg="No Search Results Found";
		}
		
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'users'=>$search_result));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));
?>
<?php
//fetching user profile 

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$fbid='1164905056865472';


	$path=BASE_PATH."uploads/";
	$sql="SELECT users.*,ifnull((select group_concat(interest SEPARATOR ',') from interests where interests.user_id=users.id and interests.interest IN (select interests.interest from interests where interests.user_id=users.id )),'') as  interest,'0 mi away' as distance,users.age as age_preference,partner_pref.user_id,partner_pref.min_age, partner_pref.max_age, education.education_level as education_preference, profession.profession_status as profession_preference, relation.rel_status as relationship_preference, food_pref.f_preference as food_preference, partner_pref.height as height_preference, sexual_pref.s_preference as sexual_preference,drinking.drinking_status as drinking_preference,smoking.smoking_status as smoking_preference, religion.religion_status as religion_preference, dating_pref.d_preference as dating_preference,(SELECT group_concat(concat('$path',gallery.image) SEPARATOR ',') as image	FROM gallery WHERE gallery.user_id=users.id) as gallery,
			CASE 
                  WHEN DATEDIFF(NOW(),users.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),users.created_on) ,' days ago')
                  WHEN HOUR(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),users.created_on)) ,' hrs ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),users.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),users.created_on)) ,' mins ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),users.created_on)) ,' s ago')
                END as last_visited
		FROM users LEFT JOIN partner_pref on partner_pref.user_id=users.id left join education on education.id=partner_pref.education_id left join profession on profession.id=partner_pref.profession_id left join relation on relation.id=partner_pref.relation_id left join food_pref on food_pref.id=partner_pref.food_pref_id left join sexual_pref on sexual_pref.id=partner_pref.sexual_pref_id left join drinking on drinking.id=partner_pref.drinking_id left join smoking on smoking.id=partner_pref.smoking_id left JOIN religion on religion.id=partner_pref.religion_id LEFT JOIN dating_pref on dating_pref.id=partner_pref.dating_pref_id  WHERE users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	try{$sth->execute();
	$success='1';
	}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	
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
			"city"=>$result[0]['city']?$result[0]['city']:"",
			"lat"=>$result[0]['lat']?$result[0]['lat']:"",
			"lang"=>$result[0]['lang']?$result[0]['lang']:"",
			"min_age"=> $result[0]['min_age']?$result[0]['min_age']:"",
			"max_age"=> $result[0]['max_age']?$result[0]['max_age']:"",
			"education_level"=> $result[0]['education_level']?$result[0]['education_level']:"",
			"profession_status"=> $result[0]['profession_status']?$result[0]['profession_status']:"",
			"rel_status"=> $result[0]['rel_status']?$result[0]['rel_status']:"",
			"food_preference"=>$result[0]['f_preference']?$result[0]['f_preference']:"",
			"drinking_status"=> $result[0]['drinking_status']?$result[0]['drinking_status']:"",
			"smoking_status"=> $result[0]['smoking_status']?$result[0]['smoking_status']:"",
			"religion_status"=> $result[0]['religion_status']?$result[0]['religion_status']:"",
			"dating_preference"=> $result[0]['d_preference']?$result[0]['d_preference']:"",
			"sexual_preference"=> $result[0]['s_preference']?$result[0]['s_preference']:"",
			"height"=> $result[0]['height']?$result[0]['height']:"",
			"distance"=> $result[0]['distance']?$result[0]['distance']:"0 mi away",
			"last_visited"=> $result[0]['last_visited']?$result[0]['last_visited']:"",
	        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
	        "access_token"=>md5($token),
			"gallery"=>$result[0]['gallery']?(array)$result[0]['gallery']:""
        );

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'profile'=>$data));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));

?>

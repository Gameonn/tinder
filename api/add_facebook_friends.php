<?php
//adding facebook friends with a user


// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");


$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

//$friend='[{"name":"Abhinandan Chada","id":"874232135947103"},{"name":"qwerty","id":"3434135947103"},{"name":"cdrf","id":"75476572135947103"}]';

$friend=$_REQUEST['facebook_friends'];
$token=$_REQUEST['token'];


if(!($friend && $token)){
$success='0';
$msg="Incomplete Parameters";
}
else{

// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$user_id=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	if($user_id){
		
		$fb_friends=json_decode($friend,true);
		if($fb_friends){
		foreach($fb_friends as $k=>$row){
		
		$sql="SELECT * FROM `facebook_friends` WHERE user_id=:user_id and friend_fbid=:friend_fbid";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('friend_fbid',$row['id']);	
		try{$sth->execute();
		$success='1';
		$msg="Facebook Friends added";
		}
		catch(PDOException $e){}
		$result[$k]=$sth->fetchAll();
		
		$fb_image='https://graph.facebook.com/'.$row["id"].'/picture';
		
		if(!count($result[$k])){
		$sql="INSERT INTO `facebook_friends`(id,user_id,friend_fbid,name,fb_image,created_on) VALUES(DEFAULT,:user_id,:friend_fbid,:name,:fb_image,NOW())";
		$sth = $conn->prepare($sql);
		$sth->bindValue('name',$row['name']);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('friend_fbid',$row['id']);
	    $sth->bindValue('fb_image',$fb_image );	
		try{$sth->execute();
		$success='1';
		$msg="Facebook Friends added";
		
		}
		catch(PDOException $e){}
		}
		}
		}
		
	
		$data=Users::fbsigninNew($fbid);
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+

if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>





























?>
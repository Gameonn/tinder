<?php
//user profile pics multiple upload in user gallery 

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$image = $_FILES['image'];
$friend=$_REQUEST['facebook_friends'];
$current_time=time();
$success=$msg="0";$data=array();
if(!($token && $image && $friend)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{

	$user_id=Users::getUserId($token);

	if($user_id){
	
		//inserting image uploads to gallery
		for($index=0; $index < count($_FILES["image"]["name"]);$index++){			
			if(!empty($_FILES["image"]["name"][$index])){
				if($_FILES["image"]["error"][$index] == 0){
					$target_dir = "";
					$pic_name = "";
					//$pic_ext = "";
					//$pic_ext = end(explode(".",$_FILES["image"]["name"][$index]));
					$pic_name = randomFileNameGenerator("Img_").".".end(explode(".",$_FILES['image']['name'][$index]));
					$target_dir = "../uploads/".$pic_name;
					if(move_uploaded_file($_FILES["image"]["tmp_name"][$index], $target_dir)){
						Users::saveImageToGallery($user_id,$pic_name);
	
					}
				}					
			}
		}
		
		$fb_friends=json_decode($friend,true);
		if($fb_friends){
		foreach($fb_friends as $k=>$row){
		
		$sql="SELECT * FROM `facebook_friends` WHERE user_id=:user_id and friend_fbid=:friend_fbid";
		$sth = $conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('friend_fbid',$row['id']);	
		try{$sth->execute();}
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
		}
		catch(PDOException $e){}
		}
		}
		}
		
		$gallery = Users::getUserGallery($user_id);
		$profile = Users::getUserProfile($user_id);
		$success='1';
		$msg="Facebook Friends and Images Uploaded";
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'gallery'=>$gallery,'profile'=>$profile));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));

function randomFileNameGenerator($prefix){
    $r=substr(str_replace(".","",uniqid($prefix,true)),0,19);
    if(file_exists("../images/$r")) randomFileNameGenerator($prefix);
    else return $r;
}
?>
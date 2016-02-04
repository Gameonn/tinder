<?php
//user profile pics multiple upload in user gallery 

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');


$token = $_REQUEST['token'];
$image = $_FILES['image'];
$success=$msg="0";$data=array();

if(!($token && $image)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{

	$user_id=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	if($user_id){
	
		//inserting image uploads to gallery
		if($image){
		
		$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
		if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
			//$success="1";
			$image_path=$randomFileName;
		}
		
			Users::saveImageToGallery($user_id,$image_path);
		
			/*for($index=0; $index < count($_FILES["image"]["name"]);$index++){			
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
			}*/
		}
		
		
		$success='1';
		$msg="Images Added to Gallery";
		$data=Users::fbsigninNew($fbid);
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'data'=>$data));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));

function randomFileNameGenerator($prefix){
    $r=substr(str_replace(".","",uniqid($prefix,true)),0,19);
    if(file_exists("../images/$r")) randomFileNameGenerator($prefix);
    else return $r;
}
?>

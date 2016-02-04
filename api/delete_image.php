<?php
//user profile pics multiple upload in user gallery 


require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');


$token = $_REQUEST['token'];
$gallery_image = $_REQUEST['image'];
$success=$msg="0";$data=array();
if(!($token && $gallery_image)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{

	$user_id=Users::getUserId($token);
	$fbid=Users::getUserfbId($token);
	
	if($user_id){
	
		//removing image uploads from gallery
		if($gallery_image){
	
		$image=str_replace('http://52.26.234.175/uploads/','',$gallery_image);

		$sql="DELETE FROM gallery WHERE user_id=:user_id and image=:image";
		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$user_id);
		$sth->bindValue("image",$image);
		try{$sth->execute();}
		catch(Exception $e){}	
		}
		
		
		$success='1';
		$msg="Images Removed From Gallery";
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

?>

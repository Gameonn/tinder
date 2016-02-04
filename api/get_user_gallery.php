<?php
//fetching user gallery pics

require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$other_id= $_REQUEST['user_id2'];
$current_time=time();
$success=$msg="0";$data=array();

if(!($token && $other_id)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{

	$user_id=Users::getUserId($token);

	if($user_id){
	
		$gallery = Users::getUserGallery($other_id);
		$profile = Users::getUserProfile($other_id);

		$success='1';
		$msg="User Gallery";
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

?>

<?php
//this is an api to unmatch user

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['token'];
$other_id=$_REQUEST['user_id2'];

if(!($token && $other_id)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	//fetching user_id and name based on token
	$user_id=Users::getUserId($token);
	
	if($user_id){
	
		//checking whether the user is already matched
		$sql="SELECT count(user_like.id) as like_count FROM `user_like` where liked_by IN (:user_id,:other_id) and liked_to IN (:user_id,:other_id) and user_like.status=1";
		$sth=$conn->prepare($sql);
		$sth->bindValue('user_id',$user_id);
		$sth->bindValue('other_id',$other_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		$like_count=$result[0]['like_count'];
		
		if($like_count==2)
		$match_status=1;
		else
		$match_status=0;
			
	GeneralFunctions::RemoveMatch($user_id,$other_id)?GeneralFunctions::RemoveMatch($user_id,$other_id):[];
	$success='1';
	$msg="Unmatched user";
	
	}
	else{
	$success='0';
	$msg="Token Expired";
	}	
}
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+

/*if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'match'=>$match));
else*/
echo json_encode(array('success'=>$success,'msg'=>$msg));
?>
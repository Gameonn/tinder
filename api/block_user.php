<?php
require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$user_id2 = $_REQUEST['user_id2'];


if(!($token && $user_id2)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{ 

	$uid=Users::getUserId($token);
	if($uid){
	
	$sql="SELECT * from blocked_users where block_by=:uid and block_to=:user_id2";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	
	if($result){
	
	$success='0';
	$msg="Already blocked this user";
	}
	else{
	
		//removing match,favourites and conversation between current user and blocked user
		GeneralFunctions::RemoveMatch($uid,$user_id2);
		GeneralFunctions::RemoveFavorites($uid,$user_id2);
		GeneralFunctions::RemoveConversation($uid,$user_id2);
	
	$sql="INSERT into blocked_users(id,block_by,block_to,created_on) VALUES(DEFAULT,:uid,:user_id2,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();
	$success='1';
	$msg="User BLocked";
	}
	catch(Exception $e){}
	}
	}
	else{
	$success='0';
	$msg="Token Expired";
	}

}
echo json_encode(array('success'=>$success,'msg'=>$msg));

?>

<?php
require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');

$token = $_REQUEST['token'];
$user_id2 = $_REQUEST['user_id2'];
$success='0';$msg='0';

if(!($token && $user_id2)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{ 

	$uid=Users::getUserId($token);
	if($uid){
	
	$sql="select * from favorite where fav_by=:uid and fav_to=:user_id2";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	
	if(count($result)){
	
	$success='0';
	$msg="Already Favorite this user";
	}
	else{
	
	$sql="INSERT into favorite(id,fav_by,fav_to,created_on) VALUES(DEFAULT,:uid,:user_id2,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();
	$success='1';
	$msg="User Favorite";
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

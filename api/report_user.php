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
	
	$sql="select * from report where report_by=:uid and report_to=:user_id2";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	
	if($result){
	
	$success='0';
	$msg="Already reported this user";
	}
	else{
	
	$sql="INSERT into report(id,report_by,report_to,created_on) VALUES(DEFAULT,:uid,:user_id2,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();
	$success='1';
	$msg="User Reported";
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

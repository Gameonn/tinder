<?php
//this is an api to add messages

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+

require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");
require_once("../GCM.php");
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'root', 'core2duo', 'codebrew_naseeb');
error_reporting(0);
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['token'];
$other_id=$_REQUEST['user_id2'];
$message=$_REQUEST['message']?$_REQUEST['message']:"";
$message_type=$_REQUEST['message_type']?$_REQUEST['message_type']:"normal";
$image=$_FILES['image'];
$video=$_FILES['video'];
$message_id=$_REQUEST['message_id']?$_REQUEST['message_id']:'0';
//$zone=$_REQUEST['zone']?$_REQUEST['zone']:'19800';


if(!($token && $other_id )){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

global $conn;

	$sql="select * from users where users.token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('token',$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	$uname=$result[0]['username'];
	
	if($user_id){
	
	if($image){
		$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
				if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
					$image_name=$randomFileName;
			}
		}
		else{
		$image_name="";
		}
		
		if($video){
		$randomFileName1=randomFileNameGenerator("Img_").".".end(explode(".",$video['name']));
				if(@move_uploaded_file($video['tmp_name'], "../uploads/$randomFileName1")){
					$video_name=$randomFileName1;
			}
		}
		else{
		$video_name="";
		}
	
	$data= Messages::saveUserMessage($user_id,$other_id,$message,$image_name,$video_name,$message_type);

	if($data){
	$success="1";
	$msg="Message Sent";
	
	$messages= Messages::getRecUserMessagesAfter($user_id,$other_id,$message_id);
	$data=$messages ? $messages:[];
	
	//push check
		$push_check=GeneralFunctions::PushCheck($other_id);
		$push_notif=$push_check[0]['push_notification'];
	
	
	if($push_notif){
	//push notification code
	$user=GeneralFunctions::get_push_ids($other_id);
	$apnid=$user[0]['apn_id'];
	$oid=$user[0]['id'];
	$reg_ids[]=$user[0]['reg_id'];

	
	$message=array();
	$message['msg']= $uname. ' sent you a message';
	$message['type']='message';//message-push
	$message['uid']=$user_id;
	$type='message';
	
	if(!empty($reg_ids)){	
	    GCM::send_notification($reg_ids, $message);
	}
				
	if(!empty($apnid)){
		try{
		$apns->newMessage($apnid);
		$apns->addMessageAlert($message['msg']);
		$apns->addMessageSound('x.wav');
		$apns->addMessageCustom('u', $user_id);
		$apns->addMessageCustom('t', $message['type']);
		$apns->queueMessage();
		$apns->processQueue();
		}
		catch(Exception $e){}
		
	}
	}
	}
	}
	else{
	$success="0";
	$msg="Token Expired";
	}
}
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"message"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));

function randomFileNameGenerator($prefix){
    $r=substr(str_replace(".","",uniqid($prefix,true)),0,19);
    if(file_exists("../images/$r")) randomFileNameGenerator($prefix);
    else return $r;
}
?>
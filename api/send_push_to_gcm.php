<?php

require_once("../php_include/config.php");
require_once("../GCM.php");

$reg_ids[]='APA91bGyIHXA78mAr9-Q4FxFBPxyQrQmMre5oWAnbchAID0_dLsegdHkgiuVhvIsCby72I6NLsU8bl5Pk6aCMO6vHecVSPNY8N9edY7pei41T6xMb4j6a0zfUc51GTEdZnajolPPgPEB';

if(!empty($reg_ids)){
	$push_data=array('push_type'=>'6','data'=>array('message'=>'Dummy push to gcm user'));
		try{
			
			GCM::send_notification($reg_ids,$push_data);
			
		}catch(Exception $e){
		//echo $e->getMessage();
		}
}
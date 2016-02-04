<?php
//this is an cron api to remove match in case there is no interaction b/w them for 24 hours after match 

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("/var/www/html/php_include/db_connection.php");
require_once("/var/www/html/classes/AllClasses.php");


global $conn;

//LCT- latest created time  MC-- message count
$sql="SELECT temp2.* FROM(SELECT temp.*,(SELECT created_on FROM user_like as UL WHERE UL.liked_by IN (temp.liked_to,temp.liked_by) AND 
UL.liked_to IN (temp.liked_by,temp.liked_to) ORDER BY created_on DESC LIMIT 0,1) as LCT,
(SELECT count(messages.id) from messages WHERE messages.user_id_sender IN (temp.liked_by,temp.liked_to) AND user_id_receiver IN (temp.liked_to,temp.liked_by)) as MC 
FROM (SELECT user_like.liked_by,user_like.liked_to,user_like.created_on FROM `user_like` JOIN users ON users.id=user_like.liked_by WHERE liked_by=users.id 
AND liked_to IN (SELECT liked_by from user_like WHERE liked_to=users.id and user_like.status=1 ) AND status=1) as temp) as temp2 WHERE temp2.MC=0 
AND DATEDIFF(CURRENT_TIMESTAMP,temp2.LCT)>=1";

$sth=$conn->prepare($sql);
try{$sth->execute();}
catch(Exception $e){}
$result=$sth->fetchAll(PDO::FETCH_ASSOC);
//result- fetching matched users with no interaction from past 24 hours

	if(count($result)){
	
		foreach($result as $key=>$value){

		$sql="DELETE FROM user_like WHERE liked_by=:liked_by AND liked_to=:liked_to";
		$sth=$conn->prepare($sql);
		$sth->bindValue('liked_by',$value['liked_by']);
		$sth->bindValue('liked_to',$value['liked_to']);
		try{$sth->execute();}
		catch(Exception $e){}
		
		}//for loop
	}//result loop



	
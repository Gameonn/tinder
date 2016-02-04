<?php
//this is an api to like question

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$val=$_REQUEST['val'];
	
	$match=Options::setDatingPrefId($val);
	echo $match;
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+

if($success)
echo json_encode(array('success'=>$success,'msg'=>$msg,'match'=>$match));
else
echo json_encode(array('success'=>$success,'msg'=>$msg));
?>
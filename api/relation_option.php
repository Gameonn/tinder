<?php
require_once('../php_include/db_connection.php');
require_once('../classes/AllClasses.php');


$option = new Options;
$relation=$option->getRelationOption();
$success='1';$msg='success';

echo json_encode(array('success'=>$success,'msg'=>$msg,'options'=>$relation));
?>
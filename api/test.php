<?php
//require_once('../facebook-php-sdk/src/Facebook/autoload.php');
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");
$fbid="1164905056865472";
$a='CAAOEVnQpa0QBAHShYXpZAmXURQTM6Bkjszh7VSB9y1tPv5CAK543ovZC8gNdxeDyAihoWj6zdf9ZCXU1rRoBuXxQSGzaMIMvJZCFwi2VkSzgl0rc3yrCivjHY4NFATToxFpmRB3KIsPRiobXyS97cHIDxZCk1UTcIU3So9yOYbtjZAQMfIJZAEhHS2JrYd6TuCiRawlt9yXu6lB0DZAkAtxSVLrSIketMO24JnxudaZAQmgZDZD';

	
	$alb=Users::getAlbumsJson($a,$fbid);
	
	$output = json_decode($alb,true);
			
		foreach($output['data'] as $k=>$row){
			if($row['name']=="Profile Pictures")
			$album_id = $row['id'];
		
		}die;


	$albums=Users::getFriendsJson($a,$fbid);
	
			$output = json_decode($albums,true);
			
		foreach($output['data'] as $k=>$row){
		echo $row['id'];
		}die;
		$albums_saved= Users::AddPhotos(8,$albums,$a);die;
/*      $url='https://graph.facebook.com/1164905056865472/friends?access_token='.$a;
      $ch=curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
echo $output;*/
/*$tre=Users::getInterestJson($a,$fbid);
echo $tre;die;
 $params = array('access_token' => $a);
        $url = "https://graph.facebook.com/".$fbid."/likes";
        $url .= '?' . http_build_query($params);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_USERAGENT ,'');
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
	
		$data= Users::AddCommonInterests(12,$output);*/
	
	
   //$output = json_decode($output,true);
  
	//foreach($output['data'] as $row){
	
	//echo $row['name'];
	//}
	//print_r($output);
	
	//$facebook->setAccessToken($a);
	//$facebook->api($page_id.'/conversations', 'GET');
	
	$params = array('access_token' => $a);
        $url = "https://graph.facebook.com/".$fbid."/photos";
        $url .= '?' . http_build_query($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_USERAGENT ,'');
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
	
	$ret = json_decode($output,true);
	
	foreach($ret['data'] as $k=>$row){
	
	echo $row['id'];die;
	
	}
	
	$url="https://graph.facebook.com/".$fbid."/photos?access_token=".$a;
	 $ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		echo $contents = curl_exec($ch);die;
		curl_close($ch);
		$image_data=json_decode($contents,true);
		print_r($image_data);die;
		
	
	
	$FBUrl = "https://graph.facebook.com/".$fbid."/albums?access_token=".$a;

 $ch = curl_init($FBUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
$contents = curl_exec($ch);
$pageData = json_decode($contents);
//object to array
$objtoarr = get_object_vars($pageData);
curl_close($ch);

	$ch = curl_init('https://graph.facebook.com/1164905056865472?fields=albums.fields(photos.fields(id,name))&access_token='.$a);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$response = curl_exec($ch);
	echo($response);die;
	var_dump(json_decode($response));
	
	 /*   $fbconfig['appid' ]  = "989931781057348";
    $fbconfig['api']  = "v2.4";
    $fbconfig['secret']  = "fe91eb714d1546e5b56ff3f6e53d6ec0";
 
    try{
        include_once "Facebook.php";
    }
    catch(Exception $o){
        echo '<pre>';
        print_r($o);
        echo '</pre>';
    }
	
    $facebook = new Facebook(array(
      'appId'  => $fbconfig['appid'],
      'secret' => $fbconfig['secret'],
      'cookie' => true,
    ));
 
    $session = $facebook->getSession();
 
    $fbme = null;
    // Session based graph API call.
    if ($session) {
      try {
        $uid = $facebook->getUser();
        $fbme = $facebook->api('/me');
      } catch (FacebookApiException $e) {
          d($e);
      }
    }
 
    function d($d){
        echo '<pre>';
        print_r($d);
        echo '</pre>';
    }*/











?>


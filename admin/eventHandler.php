<?php 
//this page is to handle all the admin events occured at client side
 require_once("../php_include/db_connection.php"); 
//require_once('PHPMailer_5.2.4/class.phpmailer.php');

/* ******         

BASIC FUNCTIONS USED
*****
*/
function randomFileNameGenerator($prefix){
  $r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
  if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
  else return $r;
}

  function sendEmail($email,$subjectMail,$bodyMail,$email_back){

    $mail = new PHPMailer(true); 
    $mail->IsSMTP(); // telling the class to use SMTP
    try {
      //$mail->Host       = SMTP_HOST; // SMTP server
      $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
      $mail->SMTPAuth   = true;                  // enable SMTP authentication
      $mail->Host       = SMTP_HOST; // sets the SMTP server
      $mail->Port       = SMTP_PORT;                    // set the SMTP port for the GMAIL server
      $mail->Username   = SMTP_USER; // SMTP account username
      $mail->Password   = SMTP_PASSWORD;        // SMTP account password
      $mail->AddAddress($email, '');     // SMTP account password
      $mail->SetFrom(SMTP_EMAIL, SMTP_NAME);
      $mail->AddReplyTo($email_back, SMTP_NAME);
      $mail->Subject = $subjectMail;
      $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';  // optional - MsgHTML will create an alternate automaticall//y
      $mail->MsgHTML($bodyMail) ;
      if(!$mail->Send()){
        $success='0';
        $msg="Error in sending mail";
      }else{
        $success='1';
      }
    } catch (phpmailerException $e) {
      $msg=$e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
      $msg=$e->getMessage(); //Boring error messages from anything else!
    }
    //echo $msg;
  }
 
    function generateRandomString($length = 6){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
    
/* ***********

END OF FUNCTIONS DECLARATION
***********
*/

/*
******
START OF CASES-- MAIN FUNCTIONALITY
******
*/
  $success=0;
  $msg="";
  session_start();
  //switch case to handle different events
  switch($_REQUEST['event']){


  case "signin":    
    $success=0;
    $user=$_REQUEST['email'];
    $password=$_REQUEST['password'];
    $redirect=$_REQUEST['redirect'];
    $sth=$conn->prepare("select * from admin where email=:email or username=:email");
    $sth->bindValue("email",$user);
    try{$sth->execute();}
    catch(Exception $e){}
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    
    
    if(count($result)){
      foreach($result as $row){
    
        if($row['password']==md5($password)){
          session_start();
          $success=1;
          
          $_SESSION['admin']['id']=$row['id'];
          $_SESSION['admin']['email']=$row['email'];
          
        }
      }
    }
    if(!$success){
      $redirect="index.php";
      $msg="Invalid Username/Password";
    }
    header("Location: $redirect?success=$success&msg=$msg");
    break;
    
  case "signout":

  session_start();
    unset($_SESSION);
    session_destroy();
    header("Location: index.php?success=1&msg=Signout Successful!");
  break;
    
} 
?>
<?php

//ini_set( 'display_errors', 1 );
//error_reporting( E_ALL );
//require_once('database_config.php');
include_once('db_config.php');
//$conn = connect();s
//$conn = ConnectionFactory::getFactory()->getConnection();
//var_dump($conn);exit();

//require_once('MysqliDb_1.php');
//error_reporting(E_ALL);
define("SITEURL", "http://".$_SERVER['HTTP_HOST']."/");
define("SITEURLPATH", "Spaysez/");
define("SITEURLDASHBOARD", "/Spaysez/dashboard.php");
define("SITEURLLOGIN", "https://paymentgateway.test.credopay.in/Spaysez/login.php");
// define("SITEQRPATH", "https://paymentgateway.test.credopay.in/Spaysez_new/");

require_once('passwordhash.php');
// $data = array();
//$db = new Mysqlidb ('paysezdb01.c33wkbq4nlhp.ap-southe/ast-1.rds.amazonaws.com', 'paysezdb', 'YjhTbhjsWEa5', 'rebanx');
// $db = new Mysqlidb ('10.162.104.214', 'urebanx', 'Rebanxpg', 'suprpaysez');
// $db = new Mysqlidb ('10.162.104.214', 'pguat', 'pguat', 'suprpaysez');
//echo "Inc Login";exit();
/*if (!$db->ping()) {
    $db->connect();
    echo "Fresh Connection"; exit();
} else {
    echo "Already Connected"; exit();
}*/

// session_start();

// echo $_SESSION['iid'];

// if(isset($_SESSION)) {
//     echo "<pre>";
//     print_r($_SESSION);
//     die();
// }


function storeUserTokens($tokens, $userId, $expiryTime){
    global $db;
    $data = Array ( 
    'user_token' => $tokens, 
    'user_id' => $userId, 
    'expriry_time' => $expiryTime,    
    );  
    $db->insert('user_tokens', $data);
    // $result = $db->insert('user_tokens', $data);
    // var_dump($db->getLastError());
    // echo $result;
    // exit;
}

function deleteUserTokens($tokens){
    global $db;
    $db->where("user_token", $tokens); 
    // $db->where("user_id", $userId); 
    $data = $db->delete("user_tokens");
}

function getUserToken($tokens){
        global $db;
        $db->where("user_token", $tokens); 
        $data = $db->getOne("user_tokens");
        return $data;
}

function getUserDetail($id){
    global $db;
    $db->where("id", $id); 
    $data = $db->getOne("users");
    return $data;
}

function loginUser($username, $password)
{
	
	
    global $db;
    $db->where("username", $username);
    //$db->where("password",$password);
    $data = $db->getOne("users");
    //$password = 'test';
    //$correct_hash = create_hash($password);
    //echo 'the hash is: '.$correct_hash.'<br />';
    //$valide = validate_password($password, $correct_hash);
    //echo 'the valide is: '.$valide;
    //var_dump($data);
	

	
    $result = validate_password($password, $data['password']);

	

    //die();
    if ($result == 1)
    {
        return $data;
    }
    else
    {
        return false;
    }
    /*
      if($db->count > 0){
      return $data;
      }else{
      return false;
      }
     */
}

// function forgotPass($email, $username)
// {
//     include_once ('PHPMailer/PHPMailerAutoload.php');
//     include_once ('PHPMailer/class.smtp.php');
//     include_once ('PHPMailer/class.pop3.php');
//     include_once ('PHPMailer/class.phpmailer.php');
//     $results_messages = array();
//     $today = date("F j, Y, g:i a T");
//     //$username = getUsernameByEmail($email);
//     $fpstring = fpstring($username);

//     try {

//         $mail = new PHPMailer(true);
//         $mail->CharSet = 'utf-8';

//         $to = $email;
//         $mail->isSMTP(); //$mail->isMail();

//         $mail->SMTPDebug   = 0; // 2 to enable SMTP debug information
//         $mail->SMTPAuth    = true; // enable SMTP authentication
//         $mail->Host        = 'smtp.gmail.com'; // Sets SMTP server
//         $mail->Port        = 587; // set the SMTP port
//         $mail->SMTPSecure  = 'tls'; //Secure conection
//         $mail->Username    = 'vesireddyramana@gmail.com'; // SMTP account username
//         $mail->Password    = 'Maheswari@143'; // SMTP account password
//         $mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
//         $mail->CharSet     = 'UTF-8';
//         $mail->Encoding    = '8bit';
//         $mail->ContentType = 'text/html; charset=utf-8\r\n';
//         $mail->From        = 'jani.shaik@itcrats.com';
//         $mail->FromName    = 'ITCrats Info Solutions Pvt Ltd.';

//         //$mail->SMTPDebug = 2;
//         //$mail->Debugoutput = 'html';
//         //$mail->Host = "smtpout.secureserver.net";
//         //$mail->Port = 465;
//         //$mail->SMTPSecure = 'ssl';
//         //$mail->SMTPAuth = true;
//         //$mail->Username = "noreply@cardfitness.com";
//         //$mail->Password = "123456";
//         //$mail->addReplyTo("noreply@earthpaysystems.com", "earthpaysystems");
//         //$mail->From = "noreply@earthpaysystems.com";
//         //$mail->FromName = "earthpaysystems";
//         $mail->addAddress($email, $username);
//         $mail->Subject = "earthpaysystems Password Request";
//         $body = "You recently requested a password reset for your earthpaysystems account. <br />
//         Your Username is : " . $username . " in-case you forgot.<br />
//         To create a new password, click on the link below:<br /><br />

//             <a href='" . SITEURL . "forgot-password.php?fpkey=" . $fpstring . "' \>Reset My Password</a><br /><br /> or copy the following URL and paste it into your browser. <br />

//              " . SITEURL . "forgot-password.php?fpkey=" . $fpstring . "
//             <br /><br />

//             This request was made on " . $today . "
//             <br /><br />

//             Regards,<br />
//             earthpaysystems<br />
//             Note: To ensure delivery to your inbox (not bulk or junk folders), please add noreply@earthpaysystems.com to your address book.";
//         $mail->WordWrap = 78;
//         $mail->msgHTML($body); //Create message bodies and embed images
        
//         if(!$mail->send()) {
//             echo "Mailer Error: " . $mail->ErrorInfo;
//         } 
//         else {
//             $mail->send();
//             echo "Message has been sent successfully";
//         }
//         //exit();
//         $results_messages[] = "Message has been sent using MAIL";
//         return true;

//     } catch (Exception $e) {
//         echo 'Message could not be sent.';
//         echo 'Mailer Error: ' . $mail->ErrorInfo;
//         return false;
//     }

// }

// function prepareDataToSendEmail() 
// {
//     $mail = new PHPMailer(true);
//     $mail->CharSet = 'utf-8';

//     //var_dump($email);

//     class phpmailerAppException extends phpmailerException {

//     }

//     $to = $email;
//     $mail->isSMTP(); //$mail->isMail();
//     //$mail->SMTPDebug = 2;
//     $mail->Debugoutput = 'html';
//     $mail->Host = "smtpout.secureserver.net";
//     $mail->Port = 465;
//     $mail->SMTPSecure = 'ssl';
//     $mail->SMTPAuth = true;
//     $mail->Username = "noreply@cardfitness.com";
//     $mail->Password = "123456";
//     $mail->addReplyTo("noreply@earthpaysystems.com", "earthpaysystems");
//     $mail->From = "noreply@earthpaysystems.com";
//     $mail->FromName = "earthpaysystems";
//     $mail->addAddress($email, $username);
//     $mail->Subject = "earthpaysystems Password Request";
//     $body = "You recently requested a password reset for your earthpaysystems account. <br />
//     Your Username is : " . $username . " in-case you forgot.<br />
//     To create a new password, click on the link below:<br /><br />

//         <a href='" . SITEURL . "forgot-password.php?fpkey=" . $fpstring . "' \>Reset My Password</a><br /><br /> or copy the following URL and paste it into your browser. <br />

//          " . SITEURL . "forgot-password.php?fpkey=" . $fpstring . "
//         <br /><br />

//         This request was made on " . $today . "
//         <br /><br />

//         Regards,<br />
//         earthpaysystems<br />
//         Note: To ensure delivery to your inbox (not bulk or junk folders), please add noreply@earthpaysystems.com to your address book.";
//     $mail->WordWrap = 78;
//     $mail->msgHTML($body); //Create message bodies and embed images

//     $mail->send();
// }

function getUsernameByEmail($email)
{
    global $db;
    $cols = Array("username");
    $db->where("email_address", $email);
    $users = $db->getOne("users", null, $cols);
    return $users['username'];
}

function ResetPass($userid, $new_hash)
{
    global $db;
    $data = array(
        'password' => $new_hash,
        'forgotpass' => NULL
    );
    $db->where('id', $userid);
    //should do some kind of security checks to prevent duplicate entries...(not in scope)
    $db->update('users', $data);
    return true;
}

function updatePassword($userid, $new_hash)
{
    global $db;
    $data = array(
        'password' => $new_hash,
        'is_first_login' => 0
    );
    $db->where('id', $userid);
    //should do some kind of security checks to prevent duplicate entries...(not in scope)
    $db->update('users', $data);
    return true;
}

function fpcheckreset($fpkey)
{
    global $db;
    $db->where("forgotpass", $fpkey);
    $users = $db->getOne("users");
    if ($db->count == 0)
    {
        return false;
    }
    return true;
}

function fpgetid($fpkey)
{
    global $db;
    $db->where("forgotpass", $fpkey);
    $users = $db->getOne("users");
    if ($db->count == 0)
    {
        return false;
    }
    return $users['id'];
}

function fpstring($username)
{
    global $db;
    if (isset($username) && strlen($username) > 0)
    {
        $today = date("F j, Y, g:i a T");
        $data = Array(
            'forgotpass' => md5($username . 'lol' . $today)
        );
        $db->where("username", $username);
        $db->update('users', $data);
        return md5($username . 'lol' . $today);
    }
}

function checkEmailUser($email, $username)
{
    global $db;
    $db->where("email_address", $email);
    $db->where("username", $username);
    $data = $db->getOne("users");

    if ($db->count == 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function checkAvailability($username)
{
    global $db;
    $db->where("username", $username);
    $data = $db->getOne("users");
    if ($db->count == 0 && $username != "")
    {
        return true;
    }
    else
    {
        return false;
    }
}

function audittrails($user_id, $event, $auditable_type, $new_values, $old_values,$url, $ip,$user_agent){



    $data = Array (
        "user_id" => $user_id,
        "event" => $event,
        "auditable_type" => $auditable_type,
        "new_values" =>$new_values,
        "old_values" => $old_values,
        "url" => $url,
        "ip_address" => $ip,
        "user_agent" => $user_agent
		);
    
	global $db;
    $db->insert('audits', $data);   
    date_default_timezone_set("Asia/Kolkata");
    $lmsg =date("d-M-Y H:i:sa") . "\n".
        "-----------------------------------" ."\n". "user_id=" . $user_id ."\n" ."event=" . $event ."\n" . "auditable_type=" . $auditable_type . "\n" ."new_values=" . $new_values ."\n" . "old_values=" . $old_values ."\n" ."&url=" . $url . "\n" ."ip_address=" . $ip ."\n" . "user_agent=" . $user_agent."\n" ;

    $logfile='auditLog/auditLog.log';
    file_put_contents($logfile,$lmsg."\n", FILE_APPEND | LOCK_EX);

}

?>

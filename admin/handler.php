<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../phpmailer/Exception.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include database configuration file  
require_once '../db-connect.php'; 

if (!empty($_GET['cmd']) && $_GET['cmd'] == 'searchwork') {
 #file_put_contents('debugsearch.txt', print_r($_GET, true));

 
 $startdate = $_GET['start'];

 /*
 $start_year =  substr($startdate, 0,4);
 $start_month =  substr($startdate, 5,2);
 $starT_day =  substr($startdate,8 ,2);
 */

 $enddate = $_GET['end'];

 /*
 $end_year =  substr($enddate, 0,4);
 $end_month =  substr($enddate, 5,2);
 $end_day =  substr($enddate,8 ,2);
 */

$sql = "SELECT FORMAT (start_date, 'yyyy-MM-dd') as Workday, 
FORMAT(cast(StartTime as time), N'hh\:mm') AS StartTime, 
FORMAT(cast(EndTime as time), N'hh\:mm') AS EndTime,
ROUND ( CAST(DATEDIFF (second,CAST(StartTime AS DATETIME),CAST(EndTime AS DATETIME)) AS FLOAT)/3600,2) AS WorkingHours,
work_reason from dbo.WorkingHours WHERE Start_date BETWEEN '$startdate' AND '$enddate' ";

$r = sqlsrv_query( $conn, $sql);



while( $row = sqlsrv_fetch_array( $r, SQLSRV_FETCH_ASSOC )) {
    $Result[]= $row;
}

#Remove 0,5 hour break

foreach ($Result as $key => $item) {
    foreach ($item as $subKey => $value) {
        if ($subKey === 'WorkingHours') {
            $Result[$key][$subKey] = $value - 0.50;
        }
    }
}


#file_put_contents('debug.txt', print_r($Result, true)); 

 echo json_encode($Result);


} elseif ( (!empty($_GET['cmd']) && $_GET['cmd'] == 'sendemail'))  {
    
    
    $MailHeader = $_POST['totaldiv'];
    $MailBody = $_POST['emailtext'];
    #file_put_contents('email.txt', print_r($MailBody, true));

    $message = $MailHeader;
    $message .= $MailBody;
    
    file_put_contents('email.txt', print_r($message, true));
    #Sending mail

    #
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'email-smtp.us-east-2.amazonaws.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'AKIAUBBUHY3VTRGANKHI';                     //SMTP username
        $mail->Password   = 'BGBX5EEVhSjvsRADNoxm/L2A8vDuwxDtSfYmhJTtscTg';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('horvathat@bryan86.hu', 'Mailer');
        $mail->addAddress('atiradeon86@gmail.com', 'Attila Horvath');     //Add a recipient
        $mail->addReplyTo('horvathat@bryan86.hu', 'Attila Horvath');
        #$mail->addCC('cc@example.com');
        #$mail->addBCC('bcc@example.com');
    
        //Attachments
        #$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Working Hours - Attila Horvath';
        $mail->Body    = "$message";
        $mail->AltBody = '-';
    
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        file_put_contents('email.txt', $mail->ErrorInfo);
    }
    
}


?>
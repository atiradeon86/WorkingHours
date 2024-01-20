<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Dompdf\Dompdf;
use Dompdf\Options;


require '../phpmailer/Exception.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require_once '../dompdf/autoload.inc.php';


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
    
    

#style
#$imageData = base64_encode(file_get_contents('../img/trogroup-logo.png'));
$style="
<html>
<head>
<style>
body {
    background: #333741;
    color: white;
    text-align: center;
}
.WorkDay {
    font-size: 18px;
    color: #007bff;
}
.ptitle {
    color: #007bff;
}

.WorkingHours {
    color: green;
}
#sum {
    display: block;
    position: relative;
    margin-bottom: 20px;
    font-size: 18px;
}
span.reason {
    color: red;
}
#logo {
        width: 300px; height: 110px;
        background-repeat: no-repeat;
        margin: 0 auto;
}
</style>
</head>
<body>
    <div id ='logo'><img src='https://apps.bryan86.hu/trogroup-app/img/trogroup-logo.png'/></div><div class='worker' style='color: green;font-size: 18px;text-align: center;'>Worker: Attila Horvath</div>";
  
    $MailHeader= $style;
    $MailHeader.=" <div id='Sum' class='sum' style ='display: block;position: relative;margin-bottom: 20px; font-size: 18px; text-align:center;'>";
    $MailHeader .= $_POST['totaldiv'];
    $MailHeader .= "</div>";
    $MailBody = $_POST['emailtext'];
    $MailFooter = "
    <body>
    </html>
    ";
    #file_put_contents('email.txt', print_r($MailBody, true));

    $message = $MailHeader;
    $message .= $MailBody;
    $message .= $MailFooter;

    $today = date("Y-m-d"); 
    file_put_contents("email-log-$today.txt", print_r($message, true));
    
    #Generate PDF
    
    $dompdf = new Dompdf();

    $dompdf->loadHtml($message);
    $dompdf->set_option('isRemoteEnabled',true);
    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    #$dompdf->stream();

    $output = $dompdf->output();
    
    file_put_contents("pdf/Attila-Horvath-$today.pdf", $output);

    
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
        $mail->addAttachment("/var/www/bryan86.hu/web/trogroup/admin/pdf/Attila-Horvath-$today.pdf");         //Add attachments
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
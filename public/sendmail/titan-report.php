<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If necess$ary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require 'vendor/autoload.php';

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender = 'support@ascendus.com';
$senderName = 'Watches - ISCM';

// Replace smtp_username with your Amazon SES SMTP user name.
$usernameSmtp = 'AKIAQ4OA42IIUGA5OPMS';

// Replace smtp_password with your Amazon SES SMTP password.
$passwordSmtp = 'BOLvsYl//xAqaQagkysTRaCwD/nVUrzslNOGzuji3YEw';

// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
$configurationSet = 'ConfigSet';

// If you're using Amazon SES in a region other than US West (Oregon),
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
// endpo$in the appropriate region.
$host = 'email-smtp.us-east-1.amazonaws.com';
$port = 587;

// The subject line of the email
$subject = '';

// The HTML-formatted body of the email
$bodyHtml = "";

$arName=array("John Smith","Ravi Chandran R","Sanjeev Kumar Jaswal","K R SWAMINATHAN","Narasimman G","Raman A","Sameer Anil Gaikwad","Sanjay Kumar Dani","Yagnavedan R","Rajeev Rajput","Girendra Kumar","Rajeswari U","Manimaran V","Jayakumar A","Jayakumar T","Kishore Batta","Dr Sathish kumar","Balasubramanian P","Duraisamy M","Vijayaragavan E","Suresh P","Krishnamoorthi K","Joe Amirtharaj A","Chandar Sampath","Raja P","MANOHAR V M","Abdul Kader T","Balaji N","Shivakumar K","Venugopal R","Krishnamurthi T V","Muthiah S","Manohar G");


$arEmail=array("saneesh@ascendus.com","ravichan@titan.co.in","sanjeevj@titan.co.in","swaminathankr@titan.co.in","narasimmang@titan.co.in","ramana@titan.co.in","sameeranilgaikwad@titan.co.in","sanjay@titan.co.in","yagnavedanr@titan.co.in","rajeevr@titan.co.in","girender@titan.co.in","rajeswari@titan.co.in","manimaran@titan.co.in","jayakumar@titan.co.in","jayakumart@titan.co.in","kishorekb@titan.co.in","drsathishakumar@titan.co.in","balasubramanian@titan.co.in","duraisamy@titan.co.in","vijayaragavan@titan.co.in","psuresh@titan.co.in","krishnamoorthik@titan.co.in","joe@titan.co.in","chandarsampath@titan.co.in","rajap@titan.co.in","manoharvm@titan.co.in","abdulk@titan.co.in","nbalaji@titan.co.in","shivakumark@titan.co.in","venugopalr@titan.co.in","krishnamurthi@titan.co.in","muthaiah@titan.co.in","manoharg@titan.co.in");


$strEmail;
        

$intMgrLoop = -1;
foreach ($arName as $strName)
{
	$intMgrLoop++;


            //Test -- 32
            if ($intMgrLoop >=0 && $intMgrLoop<0)
            	$strEmail = "";
            else
               continue;
//*/

	//if ($intMgrLoop !=88)
        //continue;

            
	$strEmail = "";
	$strEmail.="<font style='font-size: 11pt; margin: 0in 0in 0pt; line-height: normal;font-family: Calibri,sans-serif'>";
	//$strEmail.="<i>Plase ignore previous email. Sorry for the inconvenience.</i><br><br>";
	$strEmail.="Dear " . $strName . ",";

	$strEmail.="<br><br><b>Congratulations!</b> We are pleased to provide you with the results of 360-degree feedback on Culture, initiated as part of 'Building an Enabling Culture' at Watches - ISCM";
	$strEmail.="<br><br>";
	$strEmail.="Please reach out to lalithasannidhi@titan.co.in / kishorekb@titan.co.in if you have any questions about reviewing your 360 results.";
	$strEmail.="<br><br>";
	$strEmail.="Thank you,";
	$strEmail.="<br><br>";
	$strEmail.="Team Ascendus<br>";
	$strEmail.="Technology Partner, Watches - ISCM";
	$strEmail.="<br>";

    $strEmail.="</font>";

     


    $strSubject = "360 Culture Feedback Survey Report"; 
            
	//$recipient = $arEmail[$intMgrLoop];
	$recipient = 'saneesh@ascendus.com';


	$mail = new PHPMailer(true);

	try {
	// Specify the SMTP settings.
		$mail->isSMTP();
		$mail->setFrom($sender, $senderName);
		$mail->Username   = $usernameSmtp;
		$mail->Password   = $passwordSmtp;
		$mail->Host       = $host;
		$mail->Port       = $port;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'tls';
		//$mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

		
	    $mail->addattachment("Titan-Reports/".$arName[$intMgrLoop].".pdf");
		
		    


	// Specify the message recipients.
		$mail->addAddress($recipient);

		$mail->addBCC('support@ascendus.com');

		// You can also add CC, BCC, and additional To recipients here.

		// Specify the content of the message.
		$mail->isHTML(true);
		$mail->Subject    = $strSubject;
		$mail->Body       = $strEmail;
		//$mail->AltBody    = $bodyText;


		//$mail->Send();

		echo "<br>To: " . $strName."<br>Sub: " . $strSubject."<br>Email: " . $arEmail[$intMgrLoop]."<br>Attachment: " . $arName[$intMgrLoop].".pdf<br><br>";
		echo $strEmail;
		echo "<br><hr><br>";

	//echo "Email sent!" , PHP_EOL;
	} catch (phpmailerException $e) {
	echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
	} catch (Exception $e) {
	echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
	}




}

?>


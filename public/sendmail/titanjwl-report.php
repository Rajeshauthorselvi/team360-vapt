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
$senderName = 'One Jewellery';

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

$arName=array("John Smith","Deeksha Satish Shetty","Divya Barange","Divya Gupta","Geethakrishnan .","Harindranath Inturi","Hemant Pal","Mohit Pandita","Munish Kumar","Revathy Rangan","Ruchira Singh","Sana Adhami","Saravanan V","Subish Sudhakaran","Zohara Moorthy Ahluwalia");


$arEmail=array("saneesh@ascendus.com","deekshas@titan.co.in","divyab@titan.co.in","divyag@titan.co.in","geethakrishnan@titan.co.in","harindranath@titan.co.in","hemantpal@titan.co.in","mohitp@titan.co.in","munish@titan.co.in","revathyr@titan.co.in","ruchira@titan.co.in","sana@titan.co.in","saravananv@titan.co.in","subish@titan.co.in","zohara@titan.co.in");


$strEmail;
        

$intMgrLoop = -1;
foreach ($arName as $strName)
{
	$intMgrLoop++;


            //Test -- 32
            if ($intMgrLoop >=1 && $intMgrLoop<15)
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

	$strEmail.="<br><br>Please find your 360 Online Survey Feedback Report attached.";
	$strEmail.="<br><br>";
	//$strEmail.="Please reach out to venkatakrishnan@titan.co.in if you have any questions about reviewing your 360 results.";
	//$strEmail.="<br><br>";
	$strEmail.="Thank you,";
	$strEmail.="<br><br>";
	$strEmail.="Team Ascendus<br>";
	$strEmail.="Technology Partner-One Jewellery";
	$strEmail.="<br>";

    $strEmail.="</font>";

     


    $strSubject = "Leader to Legend- One Jewellery 360 Feedback Report"; 
            
	$recipient = $arEmail[$intMgrLoop];
	//$recipient = 'saneesh@ascendus.com';


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

		
	    $mail->addattachment("TitanJwl-Reports/".$arName[$intMgrLoop].".pdf");
		
		    


	// Specify the message recipients.
		$mail->addAddress($recipient);

		$mail->addBCC('support@ascendus.com');

		// You can also add CC, BCC, and additional To recipients here.

		// Specify the content of the message.
		$mail->isHTML(true);
		$mail->Subject    = $strSubject;
		$mail->Body       = $strEmail;
		//$mail->AltBody    = $bodyText;


		$mail->Send();

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


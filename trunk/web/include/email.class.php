<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require dirname(__FILE__).'/Exception.php';
require dirname(__FILE__).'/PHPMailer.php';
require dirname(__FILE__).'/SMTP.php';

function email($address,$mailtitle,$mailcontent,$html=""){
        
        global $OJ_NAME,$SMTP_SERVER,$SMTP_PORT,$SMTP_USER,$SMTP_PASS;

	
	$mail = new PHPMailer(true);
        //未经配置的系统，跳过发信步骤。
	if( $SMTP_USER != "mailer@qq.com") {      // 不要修改这个检测标记
		try {
		    //Server settings
		    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = $SMTP_SERVER;                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = $SMTP_USER;                     //SMTP username
		    $mail->Password   = $SMTP_PASS;                               //SMTP password
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
		    $mail->Port       = $SMTP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($SMTP_USER, $OJ_NAME );
		    $mail->addAddress($address, $OJ_NAME.' User');     //Add a recipient
		   // $mail->addAddress('ellen@example.com');               //Name is optional
		   // $mail->addReplyTo('info@example.com', 'Information');
		   // $mail->addCC('cc@example.com');
		   // $mail->addBCC('bcc@example.com');
		    //Attachments
		    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
		    //Content
                    if($html!=""){
			    $mail->Body= $html;
			    $mail->isHTML(true);    //Set email format to HTML
		    }else{
			    $mail->Body= $mailcontent;
			    $mail->isHTML(false);
		    }
                    $mail->AltBody = $mailcontent;
		    $mail->send();
		    echo 'Message has been sent';
		} catch (Exception $e) {
		    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}		
	}		
}

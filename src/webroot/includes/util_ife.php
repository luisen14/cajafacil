<?
function mail_attachment($files, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message, $cc, $bcc,  $sSmtpHost, $sSmtpPort, $smtpPass) 
{
	ini_set('SMTP', $sSmtpHost); 
	ini_set('smtp_port',  $sSmtpPort); 
	ini_set('sendmail_from', $from_mail);
	ini_set('password',$smtpPass);
    $uid = md5(uniqid(time()));
    $header = "From: ".$from_name." <".$from_mail.">\r\n";

    $header .= "Reply-To: ".$replyto."\r\n";
	if(trim($bcc)!="")
	{
		 $header .= "Bcc: ".$bcc."\r\n";
	}
	if(trim($cc)!="")
	{
		 $header .= "cc: ".$cc."\r\n";
	}
    $header .= "MIME-Version: 1.0\r\n";

    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

    $header .= "This is a multi-part message in MIME format.\r\n";

    $header .= "--".$uid."\r\n";

    $header .= "Content-type:text/html; charset=iso-8859-1\r\n";

    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";

    $header .= $message."\r\n\r\n";



    foreach ($files as $filename) 

    { 

        $file = $path.$filename; // path should be document root path.

        $name = basename($file);

        $file_size = filesize($file);

        $handle = fopen($file, "r");

        $content = fread($handle, $file_size);

        fclose($handle);

        $content = chunk_split(base64_encode($content));



        $header .= "--".$uid."\r\n";

        $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here

        $header .= "Content-Transfer-Encoding: base64\r\n";

        $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";

        $header .= $content."\r\n\r\n";

    }

    $header .= "--".$uid."--";
    return mail($mailto, $subject, "", $header);
}
?>
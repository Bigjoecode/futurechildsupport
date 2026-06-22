<?php
ob_start();
include_once('phpmaile/PHPMailerAutoload.php');
include 'db.php';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$project_folder = ($host == "localhost") ? "/futurechildsupport/" : "/";
$base_url = $protocol . "://" . $host . $project_folder;
	
if (isset($_POST['sendmail'])) {
  $usd = mysqli_real_escape_string($dbconnec, $_POST['uname']);
  $email = mysqli_real_escape_string($dbconnec, $_POST['email']);
  $mesg = $_POST['mesg'];
  $phone = isset($_POST['phone']) ? mysqli_real_escape_string($dbconnec, $_POST['phone']) : "Not Provided";
  $sub = isset($_POST['sub']) ? mysqli_real_escape_string($dbconnec, $_POST['sub']) : "";



// sendiong email
$sitesupport_email = 'support@futurechildsupport.com';
     
$site_domain = "futurechildsupport.com";
//from: sender name
$site_name = 'Future Child Support';
//from: sender email

//to: receiver email
$receiver_email =  $sitesupport_email;


$title = $sub;
$mail = new PHPMailer(true);
// $mail->SMTPDebug = 2;
$mail->isSMTP();                                           
$mail->Host = 'mail.futurechildsupport.com';                 
$mail->SMTPAuth = true;                               
$mail->Username   = $sitesupport_email;         
$mail->Password   = 'Chidimanager100%';                       
$mail->SMTPSecure  = 'ssl'; 
$mail->SMTPOptions = array('ssl' => array('verify_peer' => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true));
$mail->Port       = 465;  

$mail->setFrom($sitesupport_email,$site_name);
$mail->addAddress($receiver_email);
$mail->addReplyTo($sitesupport_email,$site_name);

$mail->isHTML(true);
$mail->Subject=$title;
$mail->Body='<body style="background-color: #f3f5f7; margin: 0 !important; padding: 0 !important;">
        
<!-- HIDDEN PREHEADER TEXT -->
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: "Poppins", sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">

</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<!-- LOGO -->
<tr>
  <td align="center" style="padding: 0px 10px 0px 10px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
      <tr>
      <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 10px 10px 10px;">
        <a href="#" target="_blank">
        <img alt="" src="<?php echo $base_url; ?>images/logo-wide.png"  style="max-height: 240px; max-width: 70px;" border="0">
        </a>
      </td>
      </tr>
    </table>
  </td>
</tr>
<!-- HERO -->

<tr>
  <td align="center" style="padding: 0px 10px 0px 10px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
      <tr>
        <td bgcolor="#ffffff" align="left" valign="top" style="padding: 20px 20px 10px 10px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: "Poppins", sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 2px; line-height: 48px;">
          <p style="font-size: 14px; font-weight: 600; margin: 10px 13px;">Hello Sir,</p>
        </td>
      </tr>
    </table>
  </td>
</tr>
<!-- COPY BLOCK -->
<tr>
  <td align="center" style="padding: 0px 10px 0px 10px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
      <!-- COPY -->
      <tr>
      <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 10px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
            <p style="margin:10px 13px; font-size: 14px;">'.$mesg.'</p>
      </td>
      </tr>
      
      <!-- COPY HEADING -->
      <tr>
      <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 10px 10px; color: #111111; font-family: "Poppins", sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
        <h2 style="font-size: 13px; font-weight: 600; margin: 10px 13px;">My details are as follow:</h2>
      </td>
      </tr>
      <!-- COPY -->
      <tr>
      <td bgcolor="#ffffff" align="start" style="padding: 10px 10px 10px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
      
        <p style="margin: 10px 13px; font-size: 12px;">Name: '.$usd.'</p>
        <p style="margin: 10px 13px; font-size: 12px;">Email: '. $email .'</p>
        <p style="margin: 10px 13px; font-size: 12px;">Phone Number: '. $phone .'</p>
        
      </td>
      <!-- COPY -->
      <tr>
      <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 20px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
        <p style="margin:10px 13px; font-size: 12px;">Warm regards,</p>
      </td>
      </tr>
      
      <!-- COPY -->
      <tr>
      <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 40px 10px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
        <p style="margin:10px 13px; font-size: 12px;">From ' . $site_name . '</p>
      </td>
      </tr>
    </table>
  </td>
</tr>

</table>
</body>';
try {
    $mail->send();
} catch (Exception $e) {
    // Mail failed (common on local). 
    error_log("Contact form mail failed: " . $mail->ErrorInfo);
}

$msgg = "We received your message, we will get back to you shortly.";

// Return JSON for AJAX request compatibility (contact.html)
header('Content-Type: application/json');
$status = array('status' => 'true', 'message' => $msgg);
echo json_encode($status);
exit();

}else{
header("Location:../index.html");
exit();
}
<?php
ob_start();
include 'db.php';
include_once('phpmaile/PHPMailerAutoload.php');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$project_folder = ($host == "localhost") ? "/futurechildsupport/" : "/";
$base_url = $protocol . "://" . $host . $project_folder;

if($_POST['payment_type'] == "btc_payment"){
	$item_name=mysqli_real_escape_string($dbconnec,$_POST['item_name']);
	$currency_code=mysqli_real_escape_string($dbconnec,$_POST['currency_code']);

    $fullname=mysqli_real_escape_string($dbconnec,$_POST['fullname']);
    if(!empty($_POST['company'])){
        $company=mysqli_real_escape_string($dbconnec,$_POST['company']);
    }else{
        $company = " ";
    }
    
    $country=mysqli_real_escape_string($dbconnec,$_POST['country']);
	$state=mysqli_real_escape_string($dbconnec,$_POST['state']);
	$address=mysqli_real_escape_string($dbconnec,$_POST['address']);

	$phone=mysqli_real_escape_string($dbconnec,$_POST['phone']);
	$email=mysqli_real_escape_string($dbconnec,$_POST['email']);
   
    if(!empty($_POST['note'])){
        $note=mysqli_real_escape_string($dbconnec,$_POST['note']);
    }else{
        $note = " ";
    }

    if(isset($_POST['amount'])){
      $amount = mysqli_real_escape_string($dbconnec,$_POST['amount']);
    }elseif(isset($_POST['amount1'])){
      $amount = mysqli_real_escape_string($dbconnec,$_POST['amount1']);
    }else{
      $error = "Error! Please provide an amount you will like to donate";
      header ("Location:../index.html?error=".$error);
    }

    $payment_type=mysqli_real_escape_string($dbconnec,$_POST['payment_type']);
    $img =  $_FILES['file_upload']['name'];																												
    
    $txid = uniqid();
    $txid = "Txid".substr($txid,0,3).substr($txid,-3,3);
    
    $date= date('Y-m-d H:i:s');
    $status="Processing";

    if(!isset($amount) || !isset($payment_type)){
        $error = "Error! Please select the checkbox options...";
    header ("Location:../index.html?error=".$error);
    }
    

    if(empty($_FILES['file_upload']['name'])){
        $error = "Error! Please upload your proof of payment.";
        header ("Location:../index.html?error=".$error);
        exit();
    }

    $target = "uploads/".basename($_FILES['file_upload']['name']);
    $fileType = strtolower(pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION));
    $fileSize = $_FILES['file_upload']['size'];
    $returned_val = validateImageUpload($target, $fileType, $fileSize);
if($target === $returned_val){ 

    $sql = "INSERT INTO donations (item_name, fullname, company_name, country, state, street, phone, email, amount, payment_type, proof, note, transac_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($dbconnec, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssssssss", $item_name, $fullname, $company, $country, $state, $address, $phone, $email, $amount, $payment_type, $img, $note, $txid, $status, $date);
    
    move_uploaded_file($_FILES['file_upload']['tmp_name'], $target);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error: " . mysqli_stmt_error($stmt));
        exit;
    }
    mysqli_stmt_close($stmt);

       //Mail to user on successful Donation
                        //from: site domain name
                        $site_domain = 'futurechildsupport.com';
                        //from: sender name
                        $site_name = 'Future Child Support';
                        //from: sender email
                        $sitesupport_email = "support@futurechildsupport.com";
                        //to: receiver name
                        $receiver_name = $fullname;
                        //to: receiver email
                        $receiver_email = $email;

                        $title = 'Thank you '.ucfirst($receiver_name).' - You have successfully made donation';
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
                                  <p style="font-size: 14px; font-weight: 600; margin: 10px 13px;">Hello '.$receiver_name.',</p>
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
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 20px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                    <p style="margin:10px 13px; font-size: 14px; text-align:center; color:green;">Congratulations, You have successfully made a donation with us.</p>
                              </td>
                              </tr>
                              
                              <!-- COPY HEADING -->
                              <tr>
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 40px 10px; color: #111111; font-family: "Poppins", sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
                                <h2 style="font-size: 13px; font-weight: 600; margin: 10px 13px; text-align:center; color:yellow;">The details of your donation are:</h2>
                              </td>
                              </tr>
                              <!-- COPY -->
                              <tr>
                              <td bgcolor="#ffffff" align="center" style="padding: 10px 10px 20px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                <p style="margin: 10px 13px; font-size: 12px;">Donation For: '. $item_name .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Amount Paid: '.$currency_code.' '.$amount.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Payment Type: '.$payment_type.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Transaction Id: '.$txid.'</p>
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
                        <!-- FOOTER -->
                        <tr>
                          <td align="center" style="padding: 0px 10px 50px 10px;">
                        
                          <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                            
                          <!-- PERMISSION REMINDER -->
                          <tr>
                            <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 10px 10px; color: #aaaaaa; font-family: "Poppins", sans-serif; font-size: 12px; font-weight: 400; line-height: 18px;">
                            <p style="margin:20px 13px; font-size: 12px;">We\'re here if you have any questions, drop us a line at <a href="mailto:support@futurechildsupport.com" target="_blank" style="color: #4188FA; font-weight: 700;"> anytime.</a>.</p>
                            </td>
                          </tr>
                        <!-- COPYRIGHT -->
                          <tr>
                            <td align="center" style="padding: 50px 10px 10px 10px; color: #333333; font-family: "Poppins", sans-serif; font-size: 12px; font-weight: 400; line-height: 18px;">
                            <p style="margin: 70px 0 20px; font-size: 12px;">Copyright © Future Child Support. All rights reserved.</p>
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
                          // Mail failed (common on local). Continue processing so user sees success message.
                          error_log("Mail to user failed: " . $mail->ErrorInfo);
                      }

//Mail to Adin on successful Donation
                        //from: site domain name
                        $site_domain = 'futurechildsupport.com';
                        //from: sender name
                        $site_name = 'Future Child Support';
                        //from: sender email
                        $sitesupport_email = "support@futurechildsupport.com";

                        //to: receiver email
                        $receiver_email = $sitesupport_email;

                        $title = 'Donation Alert';
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
                                  <p style="font-size: 14px; font-weight: 600; margin: 10px 13px;">Hello Sir</p>
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
                                    <p style="margin:10px 13px; font-size: 14px; text-align:center; color:green;">A client just attempt a donation.</p>
                              </td>
                              </tr>
                              
                              <!-- COPY HEADING -->
                              <tr>
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 10px 10px; color: #111111; font-family: "Poppins", sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
                                <h2 style="font-size: 13px; font-weight: 600; margin: 10px 13px; text-align:center; color:yellow;">The details of the donation are as follow:</h2>
                              </td>
                              </tr>
                              <!-- COPY -->
                              <tr>
                              <td bgcolor="#ffffff" align="center" style="padding: 10px 10px 10px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                              
                                <p style="margin: 10px 13px; font-size: 12px;">Donation For: '. $item_name .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Name of Donor: '. $fullname .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Phone Number: '. $phone .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Email Address: '. $email .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Amount Paid: '.$currency_code.' '.$amount.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Payment Type: '.$payment_type.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Transaction Id: '.$txid.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Time of donation: '.$date.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">View Proof Of Payment: <a href="'.$base_url.'secured/'.$target.'">View Payment</a></p>
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
                          error_log("Mail to admin failed: " . $mail->ErrorInfo);
                      }

  $suc = "Your donation is being processed.";
  header ("Location:../index.html?suc=".$suc);
  exit();

}else{
    $error = $returned_val;
    header("Location:../index.html?error=".$error);
}

}elseif($_POST['payment_type'] == "bank_payment"){
	$item_name=mysqli_real_escape_string($dbconnec,$_POST['item_name']);
	$currency_code=mysqli_real_escape_string($dbconnec,$_POST['currency_code']);

    $fullname=mysqli_real_escape_string($dbconnec,$_POST['fullname']);
    if(!empty($_POST['company'])){
        $company=mysqli_real_escape_string($dbconnec,$_POST['company']);
    }else{
        $company = " ";
    }
    
    $country=mysqli_real_escape_string($dbconnec,$_POST['country']);
	$state=mysqli_real_escape_string($dbconnec,$_POST['state']);
	$address=mysqli_real_escape_string($dbconnec,$_POST['address']);

	$phone=mysqli_real_escape_string($dbconnec,$_POST['phone']);
	$email=mysqli_real_escape_string($dbconnec,$_POST['email']);
   
    if(!empty($_POST['note'])){
        $note=mysqli_real_escape_string($dbconnec,$_POST['note']);
    }else{
        $note = " ";
    }

    if(isset($_POST['amount']) && !empty($_POST['amount'])){
      $amount = mysqli_real_escape_string($dbconnec,$_POST['amount']);
    }elseif(isset($_POST['amount1']) && !empty($_POST['amount1'])){
      $amount = mysqli_real_escape_string($dbconnec,$_POST['amount1']);
    }else{
      $error = "Error! Please provide an amount you will like to donate";
      header ("Location:../index.html?error=".$error);
      exit();
    }

    $payment_type=mysqli_real_escape_string($dbconnec,$_POST['payment_type']);																										
    $img = "N/A";
    $txid = uniqid();
    $txid = "Txid".substr($txid,0,3).substr($txid,-3,3);
    
    $date= date('Y-m-d H:i:s');
    $status="Processing";

    if(!isset($amount) || !isset($payment_type)){
        $error = "Error! Please select the checkbox options...";
    header ("Location:../index.html?error=".$error);
    exit();
    }
    
    $sql = "INSERT INTO donations (item_name, fullname, company_name, country, state, street, phone, email, amount, payment_type, proof, note, transac_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($dbconnec, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssssssss", $item_name, $fullname, $company, $country, $state, $address, $phone, $email, $amount, $payment_type, $img, $note, $txid, $status, $date);

    if (!mysqli_stmt_execute($stmt)) {
        die("Error: " . mysqli_stmt_error($stmt));
        exit;
    }
    mysqli_stmt_close($stmt);

       //Mail to user on successful Donation
                        //from: site domain name
                        $site_domain = 'futurechildsupport.com';
                        //from: sender name
                        $site_name = 'Future Child Support';
                        //from: sender email
                        $sitesupport_email = "support@futurechildsupport.com";
                        //to: receiver name
                        $receiver_name = $fullname;
                        //to: receiver email
                        $receiver_email = $email;

                        $title = 'Thank you '.ucfirst($receiver_name).' - You have successfully made donation';
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
                        // $mail->SMTPDebug = 2; // Enable for troubleshooting local SMTP issues

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
                                  <p style="font-size: 14px; font-weight: 600; margin: 10px 13px;">Hello '.$receiver_name.',</p>
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
                                    <p style="margin:10px 13px; font-size: 14px; text-align:center; color:green;">Thank you for reaching out to us for a donation.</p>
                              </td>
                              </tr>
                              
                              <!-- COPY HEADING -->
                              <tr>
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 10px 10px; color: #111111; font-family: "Poppins", sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
                                <h2 style="font-size: 13px; font-weight: 600; margin: 10px 13px; text-align:center; color:yellow;">You have selected to make a donation using the bank transfer method for the purpose below:</h2>
                              </td>
                              </tr>
                              <!-- COPY -->
                              <tr>
                              <td bgcolor="#ffffff" align="center" style="padding: 10px 10px 20px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                <p style="margin: 10px 13px; font-size: 12px;">Donation For: '. $item_name .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Amount Paid: '.$currency_code.' '.$amount.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Payment Type: '.$payment_type.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Transaction Id: '.$txid.'</p>
                              </td>
                              <!-- COPY -->

                              <!-- COPY HEADING -->
                              <tr>
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 40px 10px; color: #111111; font-family: "Poppins", sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
                                <h2 style="font-size: 13px; font-weight: 600; margin: 10px 13px; text-align:center;">Please be patient with us while our support team contact you.</h2>
                              </td>
                              </tr>
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
                        <!-- FOOTER -->
                        <tr>
                          <td align="center" style="padding: 0px 10px 50px 10px;">
                        
                          <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                            
                          <!-- PERMISSION REMINDER -->
                          <tr>
                            <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 10px 10px; color: #aaaaaa; font-family: "Poppins", sans-serif; font-size: 12px; font-weight: 400; line-height: 18px;">
                            <p style="margin:20px 13px; font-size: 12px;">We\'re here if you have any questions, drop us a line at <a href="mailto:support@futurechildsupport.com" target="_blank" style="color: #4188FA; font-weight: 700;"> anytime.</a>.</p>
                            </td>
                          </tr>
                        <!-- COPYRIGHT -->
                          <tr>
                            <td align="center" style="padding: 50px 10px 10px 10px; color: #333333; font-family: "Poppins", sans-serif; font-size: 12px; font-weight: 400; line-height: 18px;">
                            <p style="margin: 70px 0 20px; font-size: 12px;">Copyright © Future Child Support. All rights reserved.</p>
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
                          error_log("Mail to user failed: " . $mail->ErrorInfo);
                      }

//Mail to Adin on successful Donation
                        //from: site domain name
                        $site_domain = 'futurechildsupport.com';
                        //from: sender name
                        $site_name = 'Future Child Support';
                        //from: sender email
                        $sitesupport_email = "support@futurechildsupport.com";

                        //to: receiver email
                        $receiver_email = $sitesupport_email;

                        $title = 'Donation Alert';
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
                                  <p style="font-size: 14px; font-weight: 600; margin: 10px 13px;">Hello Sir</p>
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
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 20px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                    <p style="margin:10px 13px; font-size: 14px; text-align:center; color:green;">A client just attempt a donation by bank transfer.</p>
                              </td>
                              </tr>
                              
                              <!-- COPY HEADING -->
                              <tr>
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 40px 10px; color: #111111; font-family: "Poppins", sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
                                <h2 style="font-size: 13px; font-weight: 600; margin: 10px 13px; text-align:center; color:yellow;">The details of the donation are as follow:</h2>
                              </td>
                              </tr>
                              <!-- COPY -->
                              <tr>
                              <td bgcolor="#ffffff" align="center" style="padding: 10px 10px 20px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                              
                                <p style="margin: 10px 13px; font-size: 12px;">Donation For: '. $item_name .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Name of Donor: '. $fullname .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Phone Number: '. $phone .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Email Address: '. $email .'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Amount to Paid: '.$currency_code.' '.$amount.'</p>
                                <p style="margin: 10px 13px; font-size: 12px;">Currency selected: '.$currency_code.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Payment Type: '.$payment_type.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Transaction Id: '.$txid.'</p>
                                          <p style="margin: 10px 13px; font-size: 12px;">Time of donation: '.$date.'</p>
                                          
                              </td>
                              <!-- COPY -->
                              <tr>
                              <td bgcolor="#ffffff" align="left" style="padding: 10px 10px 20px 10px; color: #666666; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                    <p style="margin:10px 13px; font-size: 14px; text-align:center;">Please contact the client for a guide on how to complete the bank transfer process.</p>
                              </td>
                              </tr>
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
                          error_log("Mail to admin failed: " . $mail->ErrorInfo);
                      }

  $suc = "Your request is being processed. Our support team will contact you shortly.";
  header ("Location:../index.html?suc=".$suc);
  exit();

}else{
    $error = "Please select a payment method...";
    header ("Location:../index.html?error=".$error);
  exit();
}

//standard image validation
function validateImageUpload($file, $fileExe, $fileSize) {
    $exeArray = array("jpg", "png", "jpeg", "pdf");
    
    // Check extension
    if (!in_array($fileExe, $exeArray)) {
        return "File format not allowed. Please upload a JPG, PNG, or PDF file. (Detected: " . ($fileExe ?: "None") . ")";
    }

    // Check size (2MB limit)
    if ($fileSize > 2097152) {
        return "File is too large. Maximum size allowed is 2MB.";
    }

    if ($fileSize == 0) {
        return "The uploaded file is empty.";
    }

    // Check MIME type if finfo is available
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['file_upload']['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = array("image/jpeg", "image/png", "application/pdf");
        if (!in_array($mime, $allowedMimes)) {
            return "Invalid file content. The file type does not match its extension.";
        }
    }

    return $file;
}
?>
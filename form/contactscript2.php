<?php
$logfile = __DIR__ . '/mail_log.txt';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// VALUES FROM THE FORM
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['msg'])) {
    print "Error: Form data not received.";
    exit;
}

$name      = $_POST['name'];
$email     = $_POST['email'];
$message   = $_POST['msg'];
$fromemail  = "info@brownbi.com";


// ERROR & SECURITY CHECKS

if ( ( !$email ) ||

     ( strlen($_POST['email']) > 200 ) ||

     ( !preg_match("#^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$#", $email) )

   ) 

{ 

    print "Error: Invalid E-Mail Address"; 

    exit; 

} 

if ( ( !$name ) ||

     ( strlen($name) > 100 ) ||

     ( preg_match("/[:=@\<\>]/", $name) ) 

   )

{ 

    print "Error: Invalid Name"; 

    exit; 

} 

if ( preg_match("#cc:#i", $message, $matches) )

{ 

    print "Error: Found Invalid Header Field"; 

    exit; 

} 

if ( !$message )

{

    print "Error: No Message"; 

    exit; 

} 

if (preg_match("/\r|\n/", $email)) {

    print "Error: Invalid E-Mail Address"; 

    exit; 

} 

if (FALSE) { 

    print "Error: You cannot send to an email address on the same domain."; 

    exit; 

}





// CREATE THE EMAIL (using GoDaddy relay server, no authentication)
$to = "russ.brown.recruiters@gmail.com";
$subject = "New Contact Request from BrownBI - " . date('Y-m-d H:i:s');
$body = "NEW CONTACT FORM SUBMISSION\n";
$body .= "================================\n\n";
$body .= "Name: $name\n";
$body .= "Email: $email\n";
$body .= "Submitted: " . date('Y-m-d H:i:s') . "\n\n";
$body .= "Message:\n";
$body .= "--------\n";
$body .= wordwrap($message, 70) . "\n\n";
$body .= "================================\n";
$body .= "Reply directly to: $email\n";

$headers = "From: BrownBI Contact Form <info@brownbi.com>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=utf-8\r\n";
$mail_result = mail($to, $subject, $body, $headers, "-finfo@brownbi.com");
file_put_contents($logfile, date('Y-m-d H:i:s') . " | To: $to | Result: " . ($mail_result ? "SUCCESS" : "FAIL") . " | Subject: $subject | From: info@brownbi.com | Name: $name\n", FILE_APPEND);

// Send confirmation to sender (using mail())
$confirm_subject = "Thank you for contacting BrownBI";
$confirm_message = "Hi $name,\n\nThank you for reaching out to Brown Business Intelligence. We have received your message and will get back to you soon.\n\nBest regards,\nBrownBI Team";
$confirm_headers = "From: info@brownbi.com\r\nReply-To: info@brownbi.com\r\nContent-Type: text/plain; charset=iso-8859-1\r\nX-Priority: 1 (Highest)\r\nX-MSMail-Priority: High\r\nImportance: High\r\n";
$confirm_mail_result = mail($email, $confirm_subject, $confirm_message, $confirm_headers, "-finfo@brownbi.com");
file_put_contents($logfile, date('Y-m-d H:i:s') . " | To: $email | Result: " . ($confirm_mail_result ? "SUCCESS" : "FAIL") . " | Subject: $confirm_subject | From: info@brownbi.com\n", FILE_APPEND);

// REDIRECT TO THE THANKS PAGE
header("location: thanks.php");
?>


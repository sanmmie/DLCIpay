<?php
require_once "Mail.php";

$from = "Sandra Sender <noreply@example.com>";
$to = "Ramona Recipient <recipient@example.com>";
$subject = "Hi!";
$body = "Hi,\n\nHow are you?";

$host = "ssl://mail.example.com";
$port = "465";
$username = "smtp_username";
$password = "smtp_password";

$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'port' => $port,
    'auth' => true,
    'username' => $username,
    'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }
?>
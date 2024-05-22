<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['email']) &&!empty($_POST['email'])) {
    $email_to = "drufus015@gmail.com";
    $email_subject = "New Message from Website";

    function problem($error) {
        echo "Oh, looks like there is some problem with your form data: <br><br>";
        echo $error. "<br><br>";
        echo "Please fix those to proceed.<br><br>";
        die();
    }

    if (!isset($_POST['fullName']) ||!isset($_POST['email']) ||!isset($_POST['message'])) {
        problem('Oh, looks like there is some problem with your form data.');
    }

    function clean_string($string) {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $name = clean_string($_POST['fullName']);
    $email = clean_string($_POST['email']);
    $message = clean_string($_POST['message']);

    $error_message = "";

    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp, $email)) {
        $error_message.= 'Email address does not seem valid.<br>';
    }

    $string_exp = "/^[A-Za-z.'-]+$/";
    if (!preg_match($string_exp, $name)) {
        $error_message.= 'Name does not seem valid.<br>';
    }

    if (strlen($message) < 2) {
        $error_message.= 'Message should not be less than 2 characters<br>';
    }

    if (strlen($error_message) > 0) {
        problem($error_message);
    }

    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = 1;
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com'; // Set the SMTP server to send through
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@example.com'; // SMTP username
        $mail->Password   = 'your_password'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('your_email@example.com', 'Mailer');
        $mail->addAddress($email_to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $email_subject;
        $mail->Body    = "Name: ". $name. "<br>Email: ". $email. "<br>Message: ". $message;

        $mail->send();
        header('Location: thank-you.html');
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Please fill out the form.";
}
?>
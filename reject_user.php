<?php
session_start();
include 'db_config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    // Delete the user from the database
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteQuery->bind_param("i", $userId);

    if ($deleteQuery->execute()) {
        // Fetch the user's email
        $userQuery = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $userQuery->bind_param("i", $userId);
        $userQuery->execute();
        $user = $userQuery->get_result()->fetch_assoc();

        // Send rejection email
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com';
            $mail->Password = 'your-email-password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'Admin');
            $mail->addAddress($user['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Account Rejected';
            $mail->Body = 'Dear user, your account has been rejected. If you have questions, please contact support.';

            $mail->send();
            echo "<script>alert('User rejected and email sent.'); window.location.href = 'admindashboard.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('User rejected, but email could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.location.href = 'admindashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Error rejecting user.'); window.history.back();</script>";
    }

    $deleteQuery->close();
}

$conn->close();
?>

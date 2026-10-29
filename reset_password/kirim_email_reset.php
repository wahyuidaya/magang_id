<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

function kirimEmailReset($emailTujuan, $token)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'magangid666@gmail.com';
        $mail->Password = 'vvyh kfiz jsfd twuy';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('magangid666@gmail.com', 'Magang ID');
        $mail->addAddress($emailTujuan);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Kata Sandi Magang ID';

        $url = 'https://magangid.my.id/ubah_password/ubah_password.php?email=' . urlencode($emailTujuan) . '&token=' . urlencode($token);
        $mail->Body = "Klik link berikut untuk mengubah kata sandi Anda:<br><a href='$url'>$url</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>
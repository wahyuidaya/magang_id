<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

function kirimEmail($tujuan, $subjek, $pesanHTML, $emailPengirim)
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

        $mail->setFrom($emailPengirim, 'Magang ID');
        $mail->addAddress($tujuan);

        // Konten
        $mail->isHTML(true);
        $mail->Subject = $subjek;
        $mail->Body = $pesanHTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

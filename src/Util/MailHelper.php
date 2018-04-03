<?php

class MailHelper
{
    /**
     * @param $receiverMail
     * @param $subject
     * @param $body
     * @return bool
     * @throws phpmailerException
     */
    public static function sendMail($receiverMail, $subject, $body)
    {
        if (!empty(getenv('mail_enabled')) && getenv('mail_enabled') == 'true') {
            $mailer = new PHPMailer();
            $mailer->CharSet = 'UTF-8';
            $mailer->ContentType = 'text/plain';
            $mailer->isSMTP();
            $mailer->SMTPSecure = getenv('mail_secure');
            $mailer->SMTPAuth = getenv('mail_auth');
            $mailer->Host = getenv('mail_host');
            $mailer->Port = getenv('mail_port');
            $mailer->Username = getenv('mail_username');
            $mailer->Password = getenv('mail_password');
            $mailer->From = getenv('mail_from');
            $mailer->FromName = getenv('mail_from_name');
            $mailer->addAddress($receiverMail);
            $mailer->Subject = $subject;
            $mailer->Body = $body;
            return $mailer->send();
        } else {
            return false;
        }
    }
}
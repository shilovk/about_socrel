<?php
const MAIL_TO = 'shilovk@gmail.com';

//use http://www.sanwebe.com/2011/12/making-simple-jquery-ajax-contact-form
//use http://nfriedly.com/techblog/2009/11/how-to-build-a-spam-free-contact-forms-without-captchas

//check if its an ajax request, exit if not
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header("Location: /index.html");
    exit;
}

if (isset($_POST['url']) && ($_POST['url'] == '')) {
    $email = trim($_POST["email"]);
    $name = trim(strip_tags($_POST["name"]));
    $message = trim(strip_tags($_POST["message"]));
    if (($message != "") and ($name != "") and ($email != "")) {
        // prepare a "pretty" version of the message
        $body = $message;

        //Prevent Email Injection (from http://www.thesitewizard.com/php/protect-script-from-email-injection.shtml)
        if (!preg_match("/[\r\n]/", $name) && !preg_match("/[\r\n]/", $email)) {
            $from_header = toUtf8($name) . " <$email>";
            $headerFields = array(
                'MIME-Version: 1.0',
                'Content-type: text/plain; charset=UTF-8',
                "From: $from_header",
                "Reply-To: $from_header",
                'X-Mailer: PHP/' . phpversion()
            );
            $headers = implode("\r\n", $headerFields);
            $subject = 'Контакт с socrel.pstgu.ru';
            if (!mail(MAIL_TO, toUtf8($subject), $body, $headers)) {
                error_log("Could not send mail to {$email}\nHeaders:\n$headers");
                $output = json_encode(array('type'=>'error', 'text' => 'Ошибка! Попробуйте отправить сообщение позже или воспользуйтесь другими способами связи'));
                die($output);
            } else {
                $output = json_encode(array('type'=>'message', 'text' => 'Спасибо, '.$name .'! Ваше сообщение отправлено'));
                die($output);
            }
        }
    }
}

function toUtf8 ($text) {
    return '=?UTF-8?B?' . base64_encode($text) . '?=';
}
?>

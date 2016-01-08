<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Albert
 * Date: 04.10.13
 * Time: 13:53
 * To change this template use File | Settings | File Templates.
 */
class Mail{
    public function send($user_id, $title, $text){
        $email = VF::app()->database->sql("SELECT email FROM t_users WHERE id = '$user_id'")->queryScalar();
        $headers = 'From: Tooby <no-reply@tooby.ru>'."\n";
        $headers .= 'Mime-Version: 1.0' . "\n";
        $headers .= 'Content-Type: text/plain; charset= UTF-8'."\n";
        mail($email, $title, $text, $headers);
    }
}

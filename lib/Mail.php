<?php


class Mail extends Template {
    
    public static function send ($template, $data) {
    
        $render  = self::compile($template, "mail.txt");
        $subject = self::load($template, "subject.txt");
        
        if (isset($data["email"]))
            mail($data["email"], $subject, $render($data), "From: noreply@buddybeeper.net");
    }
}
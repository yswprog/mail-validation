<?php

namespace YProg\MailValidation\Contracts;

interface EmailVerifyContract
{
    /**
     * @param float $seconds
     */
    public function setMaxConnectionTimeout($seconds);

    /**
     * @param float $seconds
     */
    public function setStreamTimeout($seconds);

    /**
     * @param float $seconds
     */
    public function setStreamTimeoutWait($seconds);

    /**
     * @param $max
     */
    public function setMaximumTry($max);

    /**
     * @param string $email
     */
    public static function setEmail($email);

    /**
     * @param string $message
     */
    public function setMessage($message);


    /**
     * @param
     */
    public function getMessage();


    /**
     * @param string|null $email
     */
    public function check($email = null);

}

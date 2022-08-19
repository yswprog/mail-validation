<?php

include('vendor/autoload.php');

use YProg\MailValidation\EmailVerify;

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Check email is valid or not
$verify = EmailVerify::setEmail('yswprog@gmail.com');
$verify->check();

// Show message
echo $verify->getMessage();

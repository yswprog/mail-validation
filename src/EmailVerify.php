<?php

namespace YProg\MailValidation;

use YProg\MailValidation\Contracts\EmailVerifyContract;


/**
 * @method $this                setMaxConnectionTimeout($seconds)
 * @method $this                setStreamTimeout($seconds)
 * @method $this                setStreamTimeoutWait($seconds)
 * @method $this                setMaximumTry($max)
 * @method string               getMessage()
 * @method bool                 check($email = null)
 *
 * @method static EmailVerify   setEmail($email)
 * @method static EmailVerify   __set_state(array $array)
 *
 * </autodoc>
 */
class EmailVerify implements EmailVerifyContract
{
    use Verification;
}

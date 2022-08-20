<?php

namespace YProg\MailValidation;

trait Verification
{
    public $baseEmail = '';

    /**
     * @var int|float
     */
    public $maxConnectionTimeout = 30;

    /**
     * @var int|float
     */
    public $streamTimeout = 5;

    /**
     * @var int|float
     */
    public $streamTimeoutWait = 0;

    /**
     * @var int|float
     */
    public $maximumTry = 0;

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var string
     */
    public $email = '';

    /**
     * @var int
     */
    protected $port = 25;

    /**
     * @var bool
     */
    protected $stream = false;


    public function __construct()
    {
        $this->baseEmail = isset($_ENV['BASE_EMAIL']) ? $_ENV['BASE_EMAIL'] : 'yswprog@example.com';
    }


    /**
     * @param float $seconds
     *
     * @return $this
     */
    public function setMaxConnectionTimeout($seconds)
    {
        $this->maxConnectionTimeout = $seconds;
        return $this;
    }

    /**
     * @param float $seconds
     *
     * @return $this
     */
    public function setStreamTimeout($seconds)
    {
        $this->streamTimeout = $seconds;
        return $this;
    }

    /**
     * @param float $seconds
     *
     * @return $this
     */
    public function setStreamTimeoutWait($seconds)
    {
        $this->streamTimeoutWait = $seconds;
        return $this;
    }

    /**
     * @param float $max
     *
     * @return $this
     */
    public function setMaximumTry($max)
    {
        $this->maximumTry = $max;
        return $this;
    }

    /**
     * @param string $email
     *
     * @return static
     */
    public static function setEmail($email)
    {
        $verify = new EmailVerify();
        $verify->email = $email;

        return $verify;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string|null $email
     *
     * @return bool
     * @author CodexWorld.com <contact@codexworld.com>
     *
     */
    public function check($email = null)
    {
        if ($email) {
            $this->setEmail($email);
        }

        if (!$this->validate()) {
            return false;
        }

        $mxs = $this->getMXrecords(self::parseEmail());
        $timeout = ceil($this->maxConnectionTimeout / count($mxs));
        foreach ($mxs as $host) {

            $this->stream = @stream_socket_client("tcp://" . $host . ":" . $this->port, $errCode, $errMsg, $timeout);
            if ($this->stream === false) {

                if ($errCode == 0) {
                    $this->setMessage("Problem initializing the socket for {$this->email}");
                    return false;
                }

            } else {

                stream_set_timeout($this->stream, $this->streamTimeout);
                stream_set_blocking($this->stream, 1);

                if ($this->streamCode($this->streamResponse()) == '220') {
                    break;
                } else {
                    fclose($this->stream);
                    $this->stream = false;
                }
            }

        }

        if ($this->stream === false) {
            $this->setMessage("All connections to {$this->email} fail");
            return false;
        }

        $this->streamQuery("HELO " . self::parseEmail($this->baseEmail));
        $this->streamResponse();
        $this->streamQuery("MAIL FROM: <$this->baseEmail>");
        $this->streamResponse();
        $this->streamQuery("RCPT TO: <$this->email>");
        $code = $this->streamCode($this->streamResponse());

        fclose($this->stream);

        switch ($code) {
            case '250':
            case '450':
            case '451':
            case '452':
                $this->setMessage("{$this->email} is valid & exist.");
                return true;
            case '550':
                $this->setMessage("{$this->email} not exist!");
                return false;
            default :
                $this->setMessage("{$this->email} not exist!");
                return false;
        }
    }


    /*
     |--------------------------------------------------------------------------
     |  Functions
     |--------------------------------------------------------------------------
     */

    private function validate()
    {
        $valid = (boolean)filter_var($this->email, FILTER_VALIDATE_EMAIL);
        if (!$valid) {
            $this->setMessage("{$this->email} incorrect e-mail");
        }

        return $valid;
    }

    private function parseEmail($email = null, $domainOnly = true)
    {
        $email = $email ?: $this->email;

        sscanf($email, "%[^@]@%s", $user, $domain);
        return $domainOnly ? $domain : array($user, $domain);
    }

    private function getMXRecords($hostname)
    {
        $mxhosts = [];
        $mxweights = [];

        if (getmxrr($hostname, $mxhosts, $mxweights) !== false) {
            array_multisort($mxweights, $mxhosts);
        }

        if (empty($mxhosts)) {
            $mxhosts[] = $hostname;
        }

        return $mxhosts;
    }

    private function streamQuery($query)
    {
        return stream_socket_sendto($this->stream, "$query\r\n");
    }

    private function streamResponse($timed = 0)
    {
        $reply = stream_get_line($this->stream, 1);
        $status = stream_get_meta_data($this->stream);

        if (!empty($status['timed_out'])) {
            //
        }

        if ($reply === FALSE && $status['timed_out'] && $timed < $this->streamTimeoutWait) {
            return $this->streamResponse($timed + $this->streamTimeout);
        }


        if ($reply !== FALSE && $status['unread_bytes'] > 0) {
            $reply .= stream_get_line($this->stream, $status['unread_bytes'], "\r\n");
        }

        return $reply;
    }

    private function streamCode($str)
    {
        preg_match('/^(?<code>[0-9]{3})(\s|-)(.*)$/ims', $str, $matches);
        $code = isset($matches['code']) ? $matches['code'] : false;
        return $code;
    }

}

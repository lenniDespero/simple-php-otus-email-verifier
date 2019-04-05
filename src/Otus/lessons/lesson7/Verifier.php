<?php

namespace Otus\Lessons\Lesson7;

class Verifier {

    private $mails = array();
    private $result = array();

    public function getMails()
    {
        return $this->mails;
    }

    public function setMails($mails)
    {
        $this->mails = array_unique(is_array($mails) ? $mails : array($mails));
    }

    public function addMails($mails)
    {
        $preparedMails = is_array($mails) ? $mails : array($mails);
        $this->mails = array_unique(array_merge($this->mails, $preparedMails));
    }

    public function removeMails($mails)
    {
        $preparedMails = is_array($mails) ? $mails : array($mails);
        $this->mails = array_diff($this->mails, $preparedMails);
    }

    public function getResult()
    {
        return $this->result;
    }

    public function clearResult()
    {
        $this->result = array();
    }

    public function verify()
    {
        if (count($this->mails) > 0) {
            foreach ($this->mails as $mail) {
                try {
                    $this->checkMailString($mail);
                    $mxHosts = $this->checkMXDomain($mail);
                    $this->checkResponse($mail, $mxHosts);
                    $this->result[$mail]['checked'] = true;
                } catch (\Exception $e) {
                    $this->result[$mail]['error'] = 'Fail: ' . $e->getMessage();
                    $this->result[$mail]['checked'] = false;
                }
            }
        }
    }

    private function checkMailString(string $mail)
    {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Validation problem');
        }
    }

    private function checkMXDomain(string $mail)
    {
        $hostname = substr($mail, strrpos($mail, '@')+1);
        $mxHosts = array();
        $check = getmxrr ( $hostname ,$mxHosts);
        if (!$check) {
            throw new \Exception("No MX from Domain '$hostname'");
        }
        return $mxHosts;
    }

    private function checkResponse(string $mail, array $mxHosts)
    {
        $host = $mxHosts[0];
        $smtp_conn = fsockopen($host, 25,$errno, $errstr, 10);
        $data = $this->get_data($smtp_conn);
        var_dump($data, $errno, $errstr);
        fputs($smtp_conn,"RCPT TO:$mail\r\n");
        $data = $this->get_data($smtp_conn);
        var_dump($data);
        fputs($smtp_conn,"QUIT\r\n");
        $data = $this->get_data($smtp_conn);
        var_dump($data);
        die();
    }

    private function get_data($smtpConnection)
    {
        $data="";
        while($str = fgets($smtpConnection,515))
        {
            $data .= $str;
            if(substr($str,3,1) == " ") { break; }
        }
        return $data;
    }
}
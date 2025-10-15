<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 

 class sendMail {

    var $to;
    var $cc;
    var $bcc;
    var $subject;
    var $from;
    var $headers;
    var $html;

    function sendMail() {
        $this->to = NULL;
        $this->cc = NULL;
        $this->bcc = NULL;
        $this->subject = NULL;
        $this->from = NULL;
        $this->headers = NULL;
        $this->html = FALSE;
    }

    function getParams($params) {
        $i = 0;
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'to':
                    $this->to = $value;
                    break;
                case 'cc':
                    $this->cc = $value;
                    break;
                case 'bcc':
                    $this->bcc = $value;
                    break;
                case 'subject':
                    $this->subject = $value;
                    break;
                case 'from':
                    $this->from = $value;
                    break;
                case 'submitted':
                    NULL;
                    break;
                default:
                    $this->body = $value;
            }
        }
    }

    function setHeaders() {
        $this->headers = "From: $this->from\r\n";
        if ($this->html === TRUE) {
            $this->headers.= "MIME-Version: 1.0\r\n";
            $this->headers.= "Content-type: text/html; charset=iso-8859-1\r\n";
        }
        if (!empty($this->cc))
            $this->headers.= "Cc: $this->cc\r\n";
        if (!empty($this->bcc))
            $this->headers.= "Bcc: $this->bcc\r\n";
    }

    function send() {
        if (mail($this->to, $this->subject, $this->body, $this->headers))
            return TRUE;
        else
            return FALSE;
    }

    function set($key, $value) {
        if ($value)
            $this->$key = $value;
        else
            unset($this->$key);
    }

}
$sendmail = new sendMail();
?>

<?php namespace NozCore;

use Mailgun\HttpClientConfigurator;
use Mailgun\Mailgun;

class MailFactory {

    private $fromEmail;
    private $fromName;
    private $subject;
    private $htmlBody;
    private $textBody;

    public function __construct($subject = false, $textBody = false, $htmlBody = false, $fromEmail = false, $fromName = false) {
        $this->fromEmail = $fromEmail;
        $this->fromName  = $fromName;
        $this->subject   = $subject;
        $this->textBody  = $textBody;
        $this->htmlBody  = $htmlBody;
    }

    public function send($to = false) {
        if($to && $this->subject && ($this->textBody || $this->htmlBody) && $this->fromEmail) {
            $config = new HttpClientConfigurator();
            $config->setApiKey($GLOBALS['config']->mg['key']);

            $from = $this->fromEmail;

            if($this->fromName) {
                $from = $this->fromName . ' <' . $from . '>';
            }

            $values = [
                'from'    => $from,
                'to'      => $to,
                'subject' => $this->subject
            ];

            if($this->htmlBody) {
                $values['html'] = $this->htmlBody;
            }

            if($this->textBody) {
                $values['text'] = $this->textBody;
            }

            $mg = Mailgun::configure($config);
            $mg->messages()->send($GLOBALS['config']->mg['domain'], $values);
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param bool $htmlBody
     * @return MailFactory
     */
    public function setHtmlBody($htmlBody) {
        $this->htmlBody = $htmlBody;
        return $this;
    }

    /**
     * @param bool $textBody
     * @return MailFactory
     */
    public function setTextBody($textBody) {
        $this->textBody = $textBody;
        return $this;
    }

    /**
     * @param bool $fromEmail
     * @return MailFactory
     */
    public function setFromEmail($fromEmail) {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     * @param bool $fromName
     * @return MailFactory
     */
    public function setFromName($fromName) {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * @param bool $subject
     * @return MailFactory
     */
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }
}
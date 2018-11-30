<?php

namespace AppBundle\Service;

class SwiftMailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $swift;

    public function __construct(\Swift_Mailer $swift)
    {
        $this->swift = $swift;
    }

    public function send(string $sender, array $recipients, $subject, $body): int
    {
        $message = new \Swift_Message($subject, $body);
        $message->setSender($sender);
        $message->setTo($recipients);

        return $this->swift->send($message);
    }
}

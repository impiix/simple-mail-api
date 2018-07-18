<?php

namespace AppBundle\Service;

interface MailerInterface
{
    public function send(string $sender, array $recipients, $subject, $body): int;
}

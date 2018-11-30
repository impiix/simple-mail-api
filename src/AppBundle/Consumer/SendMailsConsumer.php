<?php

namespace AppBundle\Consumer;

use AppBundle\Service\MailService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class SendMailsConsumer implements ConsumerInterface
{
    /**
     * @var MailService
     */
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function execute(AMQPMessage $msg): void
    {
        $data = json_decode($msg->body, true);
        $this->mailService->sendById($data['id']);
        echo sprintf("Sent mail: %s\n", $data['id']);
    }
}

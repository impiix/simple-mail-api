<?php

namespace AppBundle\Service;

use AppBundle\Entity\Mail;
use AppBundle\Exception\SendException;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class MailService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProducerInterface $producer,
        MailerInterface $mailer
    ) {
        $this->entityManager = $entityManager;
        $this->producer = $producer;
        $this->mailer = $mailer;
    }

    public function createMail(Mail $mail): void
    {
        $this->entityManager->persist($mail);
        $this->entityManager->flush();
    }

    public function sendEmails(): void
    {
        $toSend = $this->entityManager
            ->getRepository(Mail::class)
            ->findBy(['status' => Mail::STATUS_TO_SEND], ['priority' => 'desc']);
        foreach ($toSend as $mail) {
            $this->producer->publish(json_encode(['id' => $mail->getId()]));
        }
    }

    public function sendById($mailId): void
    {
        $mail = $this->entityManager->getRepository(Mail::class)->find($mailId);

        $subject = filter_var($mail->getSubject(), FILTER_SANITIZE_EMAIL);
        $body = filter_var($mail->getBody(), FILTER_SANITIZE_EMAIL);

        $result = $this->mailer->send(
            $mail->getSender(),
            $mail->getRecipients(),
            $subject,
            $body
        );
        if (!$result) {
            throw new SendException("Could not send emails.");
        }
        $mail->setSent();
        $this->entityManager->persist($mail);
        $this->entityManager->flush();
    }
}

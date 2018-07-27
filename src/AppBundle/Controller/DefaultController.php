<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Mail;
use AppBundle\Entity\Recipient;
use AppBundle\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultController extends FOSRestController
{
    /**
     * @Rest\Get("/mails")
     */
    public function indexAction(EntityManagerInterface $entityManager)
    {
        $mails = $entityManager->getRepository(Mail::class)->findAll();

        return $this->handleView($this->view($mails, 200));
    }

    /**
     * @Rest\Get("/send_mails")
     */
    public function sendMailsAction(MailService $mailService)
    {
        $mailService->sendEmails();

        return $this->handleView($this->view(['ok'], 200));
    }

    /**
     * @Rest\Get("/mail/{id}")
     */
    public function getMailAction(EntityManagerInterface $entityManager, $id)
    {
        $mail = $entityManager->getRepository(Mail::class)->find($id);

        if (!$mail) {
            return $this->handleView($this->view(['error' => 'not found'], 404));
        }

        return $this->handleView($this->view($mail, 200));
    }

    /**
     * @Rest\Post("/mail")
     */
    public function postMailAction(Request $request, ValidatorInterface $validator, MailService $mailService)
    {
        if (!is_array($request->request->get("recipients"))) {
            return $this->handleView($this->view(['error' => "'recipients' values missing."], 400));
        }

        $mail = new Mail(
            $request->request->get("sender"),
            $request->request->get("status"),
            $request->request->get("priority")
        );

        foreach ($request->request->get("recipients") as $recipientAddress) {
            $recipient = new Recipient($mail, $recipientAddress);
            $mail->addRecipient($recipient);
        }

        $errors = $validator->validate($mail);

        if ($errors->count()) {
            return $this->handleView($this->view($errors, 400));
        }

        $mailService->createMail($mail);

        return $this->handleView($this->view($mail, 201));
    }
}

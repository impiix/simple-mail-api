<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Mail;
use AppBundle\Entity\Recipient;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController extends FOSRestController
{
    /**
     * @Rest\Get("/mails")
     */
    public function indexAction(Request $request)
    {
        $mails = $this->get("mail.repo")->findAll();

        return $this->handleView($this->view($mails, 200));
    }

    /**
     * @Rest\Get("/send_mails")
     */
    public function sendMailsAction(Request $request)
    {
        $this->get("mail.service")->sendEmails();

        return $this->handleView($this->view(['ok'], 200));
    }

    /**
     * @Rest\Get("/mail/{id}")
     */
    public function getMailAction(Request $request, $id)
    {
        $mail = $this->get("mail.repo")->find($id);

        if (!$mail) {
            return $this->handleView($this->view(['error' => 'not found'], 404));
        }

        return $this->handleView($this->view($mail, 200));
    }

    /**
     * @Rest\Post("/mail")
     */
    public function postMailAction(Request $request)
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

        $errors = $this->get('validator')->validate($mail);

        if ($errors->count()) {
            return $this->handleView($this->view($errors, 400));
        }

        $this->get("mail.service")->createMail($mail);

        return $this->handleView($this->view($mail, 201));
    }
}

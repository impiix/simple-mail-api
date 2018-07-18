<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="mail")
 */
class Mail
{
    const STATUS_TO_SEND = 'to_send';
    const STATUS_SENT = 'sent';

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $priority;

    /**
     * @ORM\OneToMany(targetEntity="Recipient", mappedBy="mail", cascade={"persist"})
     * @Assert\Valid()
     * @Assert\Count(
     *     min = 1
     *     )
     */
    protected $recipients;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $sender;

    /**
     * @ORM\Column(type="string")
     * @Assert\Choice(callback="getStatuses", strict=true)
     * @Assert\NotBlank()
     */
    protected $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     */
    protected $subject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @param $priority
     * @param $sender
     */
    public function __construct($sender, $status, $priority)
    {
        $this->priority = $priority;
        $this->sender = $sender;
        $this->status = $status;
        $this->recipients = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getRecipients()
    {
        return array_map(function ($el) {
            /**
             * @var Recipient $el
             */
            return $el->getAddress();
        }, $this->recipients->toArray());
    }
    
    public function getSender()
    {
        return $this->sender;
    }

    public function getStatus()
    {
        return $this->status;
    }
    
    public function getSubject()
    {
        return $this->subject;
    }
    
    public function getBody()
    {
        return $this->body;
    }

    public function addRecipient(Recipient $recipient)
    {
        $this->recipients[] = $recipient;
    }

    public static function getStatuses(): array
    {
        return [self::STATUS_SENT, self::STATUS_TO_SEND];
    }

    public function setSent()
    {
        $this->status = self::STATUS_SENT;
    }
}

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
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var ?int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $priority;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Recipient", mappedBy="mail", cascade={"persist"})
     * @Assert\Valid()
     * @Assert\Count(
     *     min = 1
     *     )
     */
    protected $recipients;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $sender;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\Choice(callback="getStatuses", strict=true)
     * @Assert\NotBlank()
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    public function __construct(
        string $sender,
        string $status,
        string $subject,
        string $body,
        ?int $priority
    ) {
        $this->priority = $priority;
        $this->sender = $sender;
        $this->status = $status;
        $this->subject = $subject;
        $this->body = $body;
        $this->recipients = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function getRecipients(): array
    {
        return array_map(function ($el) {
            /**
             * @var Recipient $el
             */
            return $el->getAddress();
        }, $this->recipients->toArray());
    }
    
    public function getSender(): string
    {
        return $this->sender;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function getSubject(): string
    {
        return $this->subject;
    }
    
    public function getBody(): string
    {
        return $this->body;
    }

    public function addRecipient(Recipient $recipient): void
    {
        $this->recipients[] = $recipient;
    }

    public static function getStatuses(): array
    {
        return [self::STATUS_SENT, self::STATUS_TO_SEND];
    }

    public function setSent(): void
    {
        $this->status = self::STATUS_SENT;
    }
}

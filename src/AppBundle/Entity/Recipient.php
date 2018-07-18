<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="recipient")
 */
class Recipient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $address;

    /**
     * @ORM\ManyToOne(targetEntity="Mail", inversedBy="recipients")
     */
    protected $mail;

    public function __construct(Mail $mail, $address)
    {
        $this->address = $address;
        $this->mail = $mail;
    }

    public function getAddress()
    {
        return $this->address;
    }
}

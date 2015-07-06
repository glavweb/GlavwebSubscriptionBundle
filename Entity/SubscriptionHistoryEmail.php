<?php

namespace Glavweb\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubscriptionHistoryEmail
 *
 * @ORM\Table(name="subscription_history_emails")
 * @ORM\Entity
 */
class SubscriptionHistoryEmail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="Glavweb\SubscriptionBundle\Entity\SubscriptionHistory", inversedBy="emails")
     * @ORM\JoinColumn(fieldName="history_id", referencedColumnName="id", nullable=false)
     */
    private $history;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return SubscriptionHistoryEmail
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set history
     *
     * @param \Glavweb\SubscriptionBundle\Entity\SubscriptionHistory $history
     * @return SubscriptionHistoryEmail
     */
    public function setHistory(\Glavweb\SubscriptionBundle\Entity\SubscriptionHistory $history)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Get history
     *
     * @return \Glavweb\SubscriptionBundle\Entity\SubscriptionHistory 
     */
    public function getHistory()
    {
        return $this->history;
    }
}

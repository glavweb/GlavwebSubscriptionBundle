<?php

namespace Glavweb\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubscriptionHistory
 *
 * @ORM\Table(name="subscription_history")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class SubscriptionHistory
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
     * @ORM\Column(name="context", type="string", length=50)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    /**
     * @var integer
     *
     * @ORM\Column(name="entity_id", type="integer")
     */
    private $entityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="count_emails", type="integer")
     */
    private $countEmails;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Glavweb\SubscriptionBundle\Entity\SubscriptionHistoryEmail", mappedBy="history")
     */
    private $emails;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set context
     *
     * @param string $context
     * @return SubscriptionHistory
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string 
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     * @return SubscriptionHistory
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SubscriptionHistory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add emails
     *
     * @param \Glavweb\SubscriptionBundle\Entity\SubscriptionHistoryEmail $emails
     * @return SubscriptionHistory
     */
    public function addEmail(\Glavweb\SubscriptionBundle\Entity\SubscriptionHistoryEmail $emails)
    {
        $this->emails[] = $emails;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param \Glavweb\SubscriptionBundle\Entity\SubscriptionHistoryEmail $emails
     */
    public function removeEmail(\Glavweb\SubscriptionBundle\Entity\SubscriptionHistoryEmail $emails)
    {
        $this->emails->removeElement($emails);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return SubscriptionHistory
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set countEmails
     *
     * @param integer $countEmails
     * @return SubscriptionHistory
     */
    public function setCountEmails($countEmails)
    {
        $this->countEmails = $countEmails;

        return $this;
    }

    /**
     * Get countEmails
     *
     * @return integer 
     */
    public function getCountEmails()
    {
        return $this->countEmails;
    }
}

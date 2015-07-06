<?php

namespace Glavweb\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Subscription
 *
 * @ORM\Table("subscriptions", uniqueConstraints={@ORM\UniqueConstraint(name="unique_idx", columns={"email", "context"})})
 * @ORM\Entity(repositoryClass="Glavweb\SubscriptionBundle\Entity\Repository\SubscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity({"email", "context"})
 */
class Subscription
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
     * @Assert\Email @Assert\NotBlank
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=200)
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15)
     * @Assert\Ip
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=50)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="unsubscribe_token", type="string", length=32)
     */
    private $unsubscribeToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return string
     */
    public function __toString()
    {
        if (!$this->getId()) {
            return 'new';
        }

        return $this->getEmail();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $tokenGenerator = new UriSafeTokenGenerator(new SecureRandom());
        
        $this->setUnsubscribeToken($tokenGenerator->generateToken());
        $this->setCreatedAt(new \DateTime());
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
     * Set email
     *
     * @param string $email
     * @return Subscription
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
     * Set ip
     *
     * @param string $ip
     * @return Subscription
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set context
     *
     * @param string $context
     * @return Subscription
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
     * Set unsubscribeToken
     *
     * @param string $unsubscribeToken
     * @return Subscription
     */
    public function setUnsubscribeToken($unsubscribeToken)
    {
        $this->unsubscribeToken = $unsubscribeToken;

        return $this;
    }

    /**
     * Get unsubscribeToken
     *
     * @return string 
     */
    public function getUnsubscribeToken()
    {
        return $this->unsubscribeToken;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Subscription
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
     * Set username
     *
     * @param string $username
     * @return Subscription
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
}

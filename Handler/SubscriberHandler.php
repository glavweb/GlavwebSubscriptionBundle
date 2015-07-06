<?php

namespace Glavweb\SubscriptionBundle\Handler;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Glavweb\SubscriptionBundle\Entity\SubscriptionHistory;
use Glavweb\SubscriptionBundle\Entity\SubscriptionHistoryEmail;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\LoggingTranslator;

/**
 * Class SubscriberHandler
 * @package Glavweb\SubscriptionBundle\Handler
 */
class SubscriberHandler
{
    /**
     * @var array
     */
    private $contextsConfig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var
     */
    private $templating;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var LoggingTranslator
     */
    private $translator;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param array $contextsConfig
     * @param \Swift_Mailer $mailer
     * @param TimedTwigEngine $templating
     * @param Registry $doctrine
     * @param LoggingTranslator $translator
     */
    public function __construct(array $contextsConfig = array(), \Swift_Mailer $mailer, TimedTwigEngine $templating, Registry $doctrine, LoggingTranslator $translator, SecurityContextInterface $securityContext)
    {
        $this->contextsConfig  = $contextsConfig;
        $this->mailer          = $mailer;
        $this->templating      = $templating;
        $this->doctrine        = $doctrine;
        $this->translator      = $translator;
        $this->securityContext = $securityContext;
    }

    /**
     * @param $context
     * @param $entity
     * @return bool
     */
    public function sendToEmails($context, $entity)
    {
        if (!isset($this->contextsConfig[$context])) {
            throw new \RuntimeException(sprintf('Context "%s" not found', $context));
        }
        $contextConfig = $this->contextsConfig[$context];

        $body = $this->templating->render($contextConfig['templates']['email_event'], array('entity' => $entity));
        $toEmails = $this->getNotSentEmails($context, $entity);
        $success = (bool)count($toEmails);

        if ($success) {
            $from = $contextConfig['from_email'];
            if ($contextConfig['from_name']) {
                $from = array($from => $contextConfig['from_name']);
            }

            $message = \Swift_Message::newInstance()
                ->setSubject($this->translator->trans($contextConfig['subject']))
                ->setFrom($from)
                ->setTo($toEmails)
                ->setBody($body, 'text/html')
            ;

            $success = (bool)$this->mailer->send($message);
            if ($success) {
                $this->createSubscriptionHistory($context, $entity, $toEmails);
            }
        }

        return $success;
    }

    /**
     * @param $context
     * @param $entity
     * @param array $emails
     */
    private function createSubscriptionHistory($context, $entity, array $emails = array())
    {
        $em = $this->doctrine->getManager();

        $history = new SubscriptionHistory();
        $history->setContext($context);
        $history->setEntityId($entity->getId());
        $history->setUsername($this->securityContext->getToken()->getUsername());
        $history->setCountEmails(count($emails));
        $em->persist($history);

        foreach ($emails as $email) {
            $historyEmail = new SubscriptionHistoryEmail();
            $historyEmail->setEmail($email);
            $historyEmail->setHistory($history);

            $em->persist($historyEmail);
        }

        $em->flush();
    }

    /**
     * @param string $context
     * @param $entity
     * @return array
     */
    private function getNotSentEmails($context, $entity)
    {
        $repository = $this->doctrine->getRepository('GlavwebSubscriptionBundle:Subscription');

        return array_map(function($subscription) {
            return $subscription->getEmail();
        }, $repository->findNotSentEmails($context, $entity->getId()));
    }
}
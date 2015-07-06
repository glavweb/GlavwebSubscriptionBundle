<?php

namespace Glavweb\SubscriptionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MailingController
 * @package Glavweb\SubscriptionBundle\Controller
 */
class MailingController extends CoreController
{
    /**
     * @Route("/mailing/{context}/{entityId}", name="glavweb_subscription_mailing")
     */
    public function mailingAction($context, $entityId)
    {
        $contexts = $this->container->getParameter('glavweb_subscription.contexts');
        if (!isset($contexts[$context])) {
            $this->createNotFoundException();
        }
        $contextConfig = $contexts[$context];

        if ($contextConfig['role'] && !$this->isGranted($contextConfig['role'])) {
            $this->createAccessDeniedException();
        }

        $em                            = $this->getDoctrine()->getManager();
        $subscriptionRepository        = $em->getRepository('GlavwebSubscriptionBundle:Subscription');
        $subscriptionHistoryRepository = $em->getRepository('GlavwebSubscriptionBundle:SubscriptionHistory');

        $entity = $em->find($contextConfig['entity_class_name'], $entityId);
        if (!$entity) {
            $this->createNotFoundException();
        }

        $countRecipients     = $subscriptionRepository->getCountRecipients($context, $entityId);
        $subscriptionHistory = $subscriptionHistoryRepository->findBy(array(
            'context'  => $context,
            'entityId' => $entityId,
        ));

        return $this->render('GlavwebSubscriptionBundle:Mailing:mailing.html.twig', array(
            'context'             => $context,
            'entity'              => $entity,
            'entityTitle'         => $contextConfig['entity_title'],
            'countRecipients'     => $countRecipients,
            'subscriptionHistory' => $subscriptionHistory
        ));
    }

    /**
     * @Route("/send-mailing", name="glavweb_subscription_send_mailing", requirements={"POST"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendMailingAction(Request $request)
    {
        $context  = $request->get('context');
        $entityId = $request->get('entity_id');

        $contexts = $this->container->getParameter('glavweb_subscription.contexts');
        if (!isset($contexts[$context])) {
            $this->createNotFoundException();
        }
        $contextConfig = $contexts[$context];

        if ($contextConfig['role'] && !$this->isGranted($contextConfig['role'])) {
            $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->find($contextConfig['entity_class_name'], $entityId);
        if (!$entity) {
            $this->createNotFoundException();
        }

        $handler = $this->get('glavweb_subscription.handler.subscriber_handler');
        $success = $handler->sendToEmails($context, $entity);

        if ($success) {
            $this->addFlash('sonata_flash_success', 'flash.send_mailing_success');
        } else {
            $this->addFlash('sonata_flash_error', 'flash.send_mailing_failure');
        }

        return $this->redirectToRoute('glavweb_subscription_mailing', array(
            'context'  => $context,
            'entityId' => $entityId
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $parameters['base_template'] = isset($parameters['base_template']) ?
            $parameters['base_template'] :
            $this->getBaseTemplate()
        ;
        $parameters['admin_pool'] = $this->get('sonata.admin.pool');

        return parent::render($view, $parameters, $response);
    }

    /**
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $message = $this->get('translator')->trans($message);

        parent::addFlash($type, $message);
    }
}

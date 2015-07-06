<?php

namespace Glavweb\SubscriptionBundle\Controller;

use Glavweb\SubscriptionBundle\Entity\Subscription;
use Glavweb\SubscriptionBundle\Form\SubscriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SubscriptionController
 * @package Glavweb\SubscriptionBundle\Controller
 */
class SubscriptionController extends Controller
{
    /**
     * @Route("/subscribe/{context}", name="glavweb_subscribe", requirements={"POST"})
     *
     * @param Request $request
     * @param string $context
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function subscribeAction(Request $request, $context)
    {
        $contexts = $this->container->getParameter('glavweb_subscription.contexts');

        if (!isset($contexts[$context])) {
            $this->createNotFoundException();
        }

        $contextConfig = $contexts[$context];
        $em            = $this->getDoctrine()->getManager();

        $subscription = new Subscription();
        $subscription->setContext($context);
        $subscription->setIp($request->getClientIp());

        $form = $this->createForm(new SubscriptionType(), $subscription);
        $form->handleRequest($request);
        if (($isValidForm = $form->isValid())) {
            $em->persist($subscription);
            $em->flush();
        }

        if ($request->isXmlHttpRequest()) {
            $error = null;
            if (!$isValidForm) {
                $errors = $form->getErrors(true);

                $error = 'Unknown error.';
                if (isset($errors[0])) {
                    $error = $errors[0] instanceof FormError ? $errors[0]->getMessage() : $errors[0];
                }
            }

            return new JsonResponse(array(
                'result' => $isValidForm,
                'error'  => $error
            ));
        }

        $returnUrl = $isValidForm ? $contextConfig['success_url'] : $contextConfig['failure_url'];
        if ($returnUrl) {
            return $this->redirect($returnUrl);
        }

        $view = $isValidForm ?
            $contextConfig['templates']['success'] :
            $contextConfig['templates']['failure']
        ;
        return $this->render($view, array(
            'context'   => $context,
            'form'      => $form->createView(),
            'returnUrl' => $form->get('return_url')->getData()
        ));
    }

    /**
     * @Route("/unsubscribe/{id}/{token}", name="glavweb_unsubscribe")
     *
     * @param int    $id
     * @param string $token
     */
    public function unsubscribeAction($id, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('GlavwebSubscriptionBundle:Subscription');

        $subscription = $repository->find($id);
        if (!$subscription || $subscription->getUnsubscribeToken() != $token) {
            $this->createNotFoundException();
        }

        $em->remove($subscription);
        $em->flush();
    }
}

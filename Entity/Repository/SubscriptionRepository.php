<?php

namespace Glavweb\SubscriptionBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * Class SubscriptionRepository
 * @package Glavweb\SubscriptionBundle\Entity\Repository
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * @param $context
     * @param $entityId
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQbGetRecipients($context, $entityId)
    {
        $historyQb = $this->getEntityManager()->getRepository('GlavwebSubscriptionBundle:SubscriptionHistory')->createQueryBuilder('h')
            ->select('e.email')
            ->innerJoin('h.emails', 'e', Expr\Join::WITH, 'e.history = h')
            ->where('h.context = :context')
            ->andWhere('h.entityId = :entityId')
        ;

        $qb = $this->createQueryBuilder('t')
            ->where('t.context = :context')
            ->andWhere('t.email NOT IN (' . $historyQb->getDQL() . ')')
            ->setParameters(array(
                'entityId' => $entityId,
                'context'  => $context
            ))
        ;

        return $qb;
    }

    /**
     * @param $context
     * @param $entityId
     * @return array
     */
    public function findNotSentEmails($context, $entityId)
    {
        $qb = $this->createQbGetRecipients($context, $entityId);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $context
     * @param $entityId
     * @return int
     */
    public function getCountRecipients($context, $entityId)
    {
        $qb = $this->createQbGetRecipients($context, $entityId)
            ->select('COUNT(t)')
        ;

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
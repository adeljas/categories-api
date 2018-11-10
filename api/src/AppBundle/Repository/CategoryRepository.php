<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\AbstractQuery;

/**
 * CustomerRequestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function getActiveRequests(){

    }

    /**
     * @param string $slug
     * @return Category
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlug(string $slug) : ?Category
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->setParameter('slug', $slug);
        $category = null;

        $query = $qb->select(array('c'))
            ->from('AppBundle:Category', 'c')
            ->where('c.slug = :slug')
            ->getQuery();


        try{
            $category = $query->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
        } catch (\Doctrine\ORM\NoResultException $e){
            // noop, function returns null object of $category
        }

        return $category;

    }
}
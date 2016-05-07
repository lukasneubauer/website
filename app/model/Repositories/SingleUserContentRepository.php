<?php

namespace App\Model\Repositories;

use App\Model\Entities;
use App\Model\Utils\PaginatorFactory;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;

abstract class SingleUserContentRepository extends BaseRepository
{
    /** @var EntityManager */
    protected $em;

    public function __construct(
        EntityDao $dao,
        EntityManager $em
    ) {
        parent::__construct($dao);

        $this->em = $em;
    }

    /**
     * @param  Entities\BaseEntity $e
     * @return Entities\BaseEntity
     */
    public function activate(Entities\BaseEntity $e)
    {
        $e->isActive = true;

        $this->persistAndFlush($this->em, $e);

        return $e;
    }

    /**
     * @param  string           $className
     * @param  PaginatorFactory $paginatorFactory
     * @param  int              $page
     * @param  int              $limit
     * @param  bool             $activeOnly
     * @return Paginator
     */
    protected function doGetAllForPage($className, PaginatorFactory $paginatorFactory, $page, $limit, $activeOnly = false)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('e')
            ->from($className, 'e');

        if ($activeOnly) {
            $qb->where('e.isActive = :state')
                ->setParameter('state', true);
        }

        $this->preparePagination($qb, $page, $limit);

        return $paginatorFactory->createPaginator($qb->getQuery());
    }

    /**
     * @param  string                   $className
     * @param  Entities\TagEntity       $tag
     * @return Entities\ArticleEntity[]
     */
    protected function doGetAllByTag($className, Entities\TagEntity $tag)
    {
        return $this->dao->createQueryBuilder()
            ->select('e')
            ->from($className, 'e')
            ->join('e.tag', 't')
            ->where('t.id = :tagId')
            ->setParameter('tagId', $tag->id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  string                      $className
     * @param  Entities\TagEntity          $tag
     * @param  string                      $name
     * @return Entities\ArticleEntity|null
     */
    protected function doGetByTagAndName($className, Entities\TagEntity $tag, $name)
    {
        try {
            return $this->dao->createQueryBuilder()
                ->select('e')
                ->from($className, 'e')
                ->join('e.tag', 't')
                ->where('t.id = :tagId AND e.name = :name')
                ->setParameters(array(
                    'tagId' => $tag->id,
                    'name'  => $name,
                ))
                ->getQuery()
                ->getSingleResult();

        } catch (NonUniqueResultException $e) {
            return null;

        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param  string                      $className
     * @param  Entities\TagEntity          $tag
     * @param  string                      $slug
     * @return Entities\ArticleEntity|null
     */
    protected function doGetByTagAndSlug($className, Entities\TagEntity $tag, $slug)
    {
        try {
            return $this->dao->createQueryBuilder()
                ->select('e')
                ->from($className, 'e')
                ->join('e.tag', 't')
                ->where('t.id = :tagId AND e.slug = :slug')
                ->setParameters(array(
                    'tagId' => $tag->id,
                    'slug'  => $slug,
                ))
                ->getQuery()
                ->getSingleResult();

        } catch (NonUniqueResultException $e) {
            return null;

        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param  string             $className
     * @param  PaginatorFactory   $paginatorFactory
     * @param  int                $page
     * @param  int                $limit
     * @param  Entities\TagEntity $tag
     * @param  bool               $activeOnly
     * @return Paginator
     */
    protected function doGetAllByTagForPage($className, PaginatorFactory $paginatorFactory, $page, $limit, Entities\TagEntity $tag, $activeOnly = false)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('e')
            ->from($className, 'e')
            ->join('e.tag', 't')
            ->where('t.id = :tagId');

        $params = array('tagId' => $tag->id);

        if ($activeOnly) {
            $qb->andWhere('e.isActive = :state');
            $params['state'] = true;
        }

        $qb->setParameters($params);

        $this->preparePagination($qb, $page, $limit);

        return $paginatorFactory->createPaginator($qb->getQuery());
    }

    /**
     * @param  string              $className
     * @param  PaginatorFactory    $paginatorFactory
     * @param  int                 $page
     * @param  int                 $limit
     * @param  Entities\UserEntity $user
     * @return Paginator
     */
    protected function doGetAllByUserForPage($className, PaginatorFactory $paginatorFactory, $page, $limit, Entities\UserEntity $user)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('e')
            ->from($className, 'e')
            ->join('e.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->id);

        $this->preparePagination($qb, $page, $limit);

        return $paginatorFactory->createPaginator($qb->getQuery());
    }

    /**
     * @param  string           $className
     * @param  PaginatorFactory $paginatorFactory
     * @param  int              $page
     * @param  int              $limit
     * @return Paginator
     */
    protected function doGetAllInactiveForPage($className, PaginatorFactory $paginatorFactory, $page, $limit)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('e')
            ->from($className, 'e')
            ->where('e.isActive = :state')
            ->setParameter('state', false);

        $this->preparePagination($qb, $page, $limit);

        return $paginatorFactory->createPaginator($qb->getQuery());
    }

    /**
     * @param  string             $className
     * @param  PaginatorFactory   $paginatorFactory
     * @param  int                $page
     * @param  int                $limit
     * @param  Entities\TagEntity $tag
     * @return Paginator
     */
    protected function doGetAllInactiveByTagForPage($className, PaginatorFactory $paginatorFactory, $page, $limit, Entities\TagEntity $tag)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('e')
            ->from($className, 'e')
            ->join('e.tag', 't')
            ->where('t.id = :tagId AND e.isActive = :state')
            ->setParameters(array(
                'tagId' => $tag->id,
                'state' => false,
            ));

        $this->preparePagination($qb, $page, $limit);

        return $paginatorFactory->createPaginator($qb->getQuery());
    }
}

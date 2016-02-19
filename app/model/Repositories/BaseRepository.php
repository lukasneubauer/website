<?php

namespace App\Model\Repositories;

use App\Model\Entities;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;

abstract class BaseRepository
{
    /** @var EntityDao */
    protected $dao;

    public function __construct(EntityDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param  array                 $criteria
     * @param  array                 $orderBy
     * @param  int                   $limit
     * @param  int                   $offset
     * @return Entities\BaseEntity[]
     */
    public function getAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->dao->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param  int                 $id
     * @return Entities\BaseEntity
     */
    public function getById($id)
    {
        return $this->dao->find($id);
    }

    /**
     * @param  array                 $criteria
     * @return Entities\BaseEntity[]
     */
    public function getCount(array $criteria = array())
    {
        return $this->dao->countBy($criteria);
    }

    /**
     * @param EntityManager       $em
     * @param Entities\BaseEntity $e
     */
    protected function persistAndFlush(EntityManager $em, Entities\BaseEntity $e)
    {
        $em->persist($e);
        $em->flush();
    }
}

<?php

namespace App\Model\Repositories;

use App\Model\Duplicities\DuplicityChecker;
use App\Model\Duplicities\PossibleUniqueKeyDuplicationException;
use App\Model\Entities;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use HeavenProject\Utils\Slugger;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

class WikiRepository extends BaseRepository
{
    use DuplicityChecker;

    /** @var ITranslator */
    private $translator;

    /** @var EntityManager */
    private $em;

    public function __construct(
        EntityDao $dao,
        ITranslator $translator,
        EntityManager $em
    ) {
        parent::__construct($dao);

        $this->translator = $translator;
        $this->em         = $em;
    }

    /**
     * @param  ArrayHash $values
     * @param  Entities\TagEntity $tag
     * @param  Entities\UserEntity $user
     * @param  string $type
     * @param  Entities\WikiEntity $e
     * @throws PossibleUniqueKeyDuplicationException
     * @return Entities\WikiEntity
     */
    public function create(
        ArrayHash $values,
        Entities\TagEntity $tag,
        Entities\UserEntity $user,
        $type,
        Entities\WikiEntity $e
    ) {
        $e->setValues($values);

        if ($this->getByTagAndNameAndType($tag, $values->name, $type)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_name')
            );
        }

        $e->slug = $e->slug ?: Slugger::slugify($e->name);

        if ($this->getByTagAndSlugAndType($tag, $e->slug, $type)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_slug')
            );
        }

        $e->createdBy = $user;
        $e->tag       = $tag;
        $e->type      = $type;

        $this->em->persist($e);
        $this->em->flush();

        return $e;
    }

    /**
     * @param  ArrayHash $values
     * @param  Entities\TagEntity $tag
     * @param  string $type
     * @param  Entities\WikiEntity $e
     * @throws PossibleUniqueKeyDuplicationException
     * @return Entities\WikiEntity
     */
    public function update(
        ArrayHash $values,
        Entities\TagEntity $tag,
        $type,
        Entities\WikiEntity $e
    ) {
        $e->setValues($values);

        if ($e->tag->id !== $tag->id && $this->getByTagAndNameAndType($tag, $values->name, $type)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_name')
            );
        }

        $e->slug = $e->slug ?: Slugger::slugify($e->name);

        if ($e->tag->id !== $tag->id && $this->getByTagAndSlugAndType($tag, $e->slug, $type)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_slug')
            );
        }

        $e->tag  = $tag;
        $e->type = $type;

        $this->em->persist($e);
        $this->em->flush();

        return $e;
    }

    /**
     * @param  int       $page
     * @param  int       $limit
     * @param  string    $type
     * @param  bool      $activeOnly
     * @return Paginator
     */
    public function getAllForPage($page, $limit, $type, $activeOnly = false)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('w')
            ->from(Entities\WikiEntity::getClassName(), 'w')
            ->where('w.type = :type');

        $params = array('type' => $type);

        if ($activeOnly) {
            $qb->andWhere('w.isActive = :state');
            $params['state'] = true;
        }

        $qb->setParameters($params)
            ->setFirstResult($page * $limit - $limit)
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    /**
     * @param  Entities\TagEntity    $tag
     * @param  string                $type
     * @return Entities\WikiEntity[]
     */
    public function getAllByTag(Entities\TagEntity $tag, $type)
    {
        return $this->dao->createQueryBuilder()
            ->select('w')
            ->from(Entities\WikiEntity::getClassName(), 'w')
            ->join('w.tag', 't')
            ->where('t.id = :tagId AND w.type = :type')
            ->setParameters(array(
                'tagId' => $tag->id,
                'type'  => $type,
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  Entities\TagEntity       $tag
     * @param  string                   $name
     * @return Entities\WikiEntity|null
     */
    public function getByTagAndName(Entities\TagEntity $tag, $name)
    {
        try {
            return $this->dao->createQueryBuilder()
                ->select('w')
                ->from(Entities\WikiEntity::getClassName(), 'w')
                ->join('w.tag', 't')
                ->where('t.id = :tagId AND w.name = :name')
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
     * @param  Entities\TagEntity       $tag
     * @param  string                   $slug
     * @return Entities\WikiEntity|null
     */
    public function getByTagAndSlug(Entities\TagEntity $tag, $slug)
    {
        try {
            return $this->dao->createQueryBuilder()
                ->select('w')
                ->from(Entities\WikiEntity::getClassName(), 'w')
                ->join('w.tag', 't')
                ->where('t.id = :tagId AND w.slug = :slug')
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
     * @param  Entities\TagEntity       $tag
     * @param  string                   $name
     * @param  string                   $type
     * @return Entities\WikiEntity|null
     */
    public function getByTagAndNameAndType(Entities\TagEntity $tag, $name, $type)
    {
        try {
            return $this->dao->createQueryBuilder()
                ->select('w')
                ->from(Entities\WikiEntity::getClassName(), 'w')
                ->join('w.tag', 't')
                ->where('t.id = :tagId AND w.name = :name AND w.type = :type')
                ->setParameters(array(
                    'tagId' => $tag->id,
                    'name'  => $name,
                    'type'  => $type,
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
     * @param  Entities\TagEntity       $tag
     * @param  string                   $slug
     * @param  string                   $type
     * @return Entities\WikiEntity|null
     */
    public function getByTagAndSlugAndType(Entities\TagEntity $tag, $slug, $type)
    {
        try {
            return $this->dao->createQueryBuilder()
                ->select('w')
                ->from(Entities\WikiEntity::getClassName(), 'w')
                ->join('w.tag', 't')
                ->where('t.id = :tagId AND w.slug = :slug AND w.type = :type')
                ->setParameters(array(
                    'tagId' => $tag->id,
                    'slug'  => $slug,
                    'type'  => $type,
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
     * @param  int                $page
     * @param  int                $limit
     * @param  Entities\TagEntity $tag
     * @param  string             $type
     * @param  bool               $activeOnly
     * @return Paginator
     */
    public function getAllByTagForPage($page, $limit, Entities\TagEntity $tag, $type, $activeOnly = false)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('w')
            ->from(Entities\WikiEntity::getClassName(), 'w')
            ->join('w.tag', 't')
            ->where('t.id = :tagId AND w.type = :type');

        $params = array(
            'tagId' => $tag->id,
            'type'  => $type,
        );

        if ($activeOnly) {
            $qb->andWhere('w.isActive = :state');
            $params['state'] = true;
        }

        $qb->setParameters($params)
            ->setFirstResult($page * $limit - $limit)
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    /**
     * @param  int                 $page
     * @param  int                 $limit
     * @param  Entities\UserEntity $user
     * @param  string              $type
     * @return Paginator
     */
    public function getAllByUserForPage($page, $limit, Entities\UserEntity $user, $type)
    {
        $qb = $this->dao->createQueryBuilder()
            ->select('w')
            ->from(Entities\WikiEntity::getClassName(), 'w')
            ->join('w.createdBy', 'u')
            ->where('u.id = :userId AND w.type = :type')
            ->setParameters(array(
                'userId' => $user->id,
                'type'   => $type,
            ))
            ->setFirstResult($page * $limit - $limit)
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    /**
     * @param  int            $page
     * @param  int            $limit
     * @param  string         $type
     * @return Paginator|null
     */
    public function getAllWithDraftsForPage($page, $limit, $type)
    {
        $wikiIds = $this->getIdListOfWikisThatHaveDrafts();

        if (!$wikiIds) {
            return null;
        }

        $qb = $this->dao->createQueryBuilder()
            ->select('w')
            ->from(Entities\WikiEntity::getClassName(), 'w')
            ->leftJoin('w.drafts', 'd')
            ->where('w.type = :type');

        $qb->andWhere(
            $qb->expr()->in('w.id', $wikiIds)
        );

        $qb->setParameter('type', $type)
            ->setFirstResult($page * $limit - $limit)
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    /**
     * @return array
     */
    public function getIdListOfWikisThatHaveDrafts()
    {
        $qb = $this->dao->createQueryBuilder();
        $qb->select('w.id')
            ->from(Entities\WikiDraftEntity::getClassName(), 'd')
            ->leftJoin('d.wiki', 'w')
            ->distinct('w.id')
            ->where(
                $qb->expr()->isNotNull('w.id')
            );

        $res = array();

        foreach ($qb->getQuery()->getResult() as $i) {
            $res[] = $i['id'];
        }

        return $res;
    }
}

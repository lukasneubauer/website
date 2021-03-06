<?php

namespace App\Repositories;

use App\Caching;
use App\Dao\SingleUserContentDao;
use App\Duplicities\PossibleUniqueKeyDuplicationException;
use App\Entities;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

class ArticleRepository extends SingleUserContentRepository
{
    /** @var ITranslator */
    private $translator;

    /** @var BacklinkRepository */
    private $backlinkRepository;

    /** @var \HtmlPurifier */
    private $htmlPurifier;

    public function __construct(
        EntityDao $dao,
        SingleUserContentDao $dataAccess,
        ITranslator $translator,
        EntityManager $em,
        Caching\ArticleTagSectionCache $tagCache,
        BacklinkRepository $backlinkRepository,
        \HTMLPurifier $htmlPurifier
    ) {
        parent::__construct($dao, $dataAccess, $em, $tagCache);

        $this->translator         = $translator;
        $this->backlinkRepository = $backlinkRepository;
        $this->htmlPurifier       = $htmlPurifier;
    }

    /**
     * @param  ArrayHash                             $values
     * @param  Entities\TagEntity                    $tag
     * @param  Entities\UserEntity                   $user
     * @param  Entities\ArticleEntity                $e
     * @throws PossibleUniqueKeyDuplicationException
     * @return Entities\ArticleEntity
     */
    public function create(
        ArrayHash $values,
        Entities\TagEntity $tag,
        Entities\UserEntity $user,
        Entities\ArticleEntity $e
    ) {
        $e->setValues($values);

        if ($this->getByTagAndName($tag, $values->name)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_name')
            );
        }

        $e->slug = $e->slug ?: Strings::webalize($e->name);

        if ($this->getByTagAndSlug($tag, $e->slug)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_slug')
            );
        }

        $e->text = $this->htmlPurifier->purify($values->text);
        $e->tag  = $tag;
        $e->user = $user;

        return $this->persistAndFlush($this->em, $e);
    }

    /**
     * @param  ArrayHash                             $values
     * @param  Entities\TagEntity                    $tag
     * @param  Entities\UserEntity                   $user
     * @param  Entities\ArticleEntity                $e
     * @throws PossibleUniqueKeyDuplicationException
     * @return Entities\ArticleEntity
     */
    public function update(
        ArrayHash $values,
        Entities\TagEntity $tag,
        Entities\UserEntity $user,
        Entities\ArticleEntity $e
    ) {
        $oldTag  = $e->tag;
        $oldSlug = $e->slug;

        if ($e->tag->id !== $tag->id) {
            $this->tagCache->deleteSection();
        }

        $e->setValues($values);

        if ($e->tag->id !== $tag->id && $this->getByTagAndName($tag, $values->name)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_name')
            );
        }

        $e->slug = $e->slug ?: Strings::webalize($e->name);

        if ($e->tag->id !== $tag->id && $this->getByTagAndSlug($tag, $e->slug)) {
            throw new PossibleUniqueKeyDuplicationException(
                $this->translator->translate('locale.duplicity.article_tag_and_slug')
            );
        }

        $e->text = $this->htmlPurifier->purify($values->text);
        $e->tag  = $tag;
        $e->user = $user;

        $ent = $this->persistAndFlush($this->em, $e);

        $this->backlinkRepository->create($ent, $oldTag, $oldSlug, 'clanky');

        return $ent;
    }

    /**
     * @param  Entities\ArticleEntity $e
     * @return Entities\ArticleEntity
     */
    public function activate(Entities\ArticleEntity $e)
    {
        return $this->doActivate($e);
    }

    /**
     * @param  Entities\ArticleEntity $e
     * @return Entities\ArticleEntity
     */
    public function delete(Entities\ArticleEntity $e)
    {
        $ent = $this->removeAndFlush($this->em, $e);

        $this->tagCache->deleteSection();

        return $ent;
    }

    /**
     * @param  int       $page
     * @param  int       $limit
     * @param  bool      $activeOnly
     * @return Paginator
     */
    public function getAllForPage($page, $limit, $activeOnly = false)
    {
        return $this->dataAccess->getAllForPage(Entities\ArticleEntity::class, $page, $limit, $activeOnly);
    }

    /**
     * @param  Entities\TagEntity       $tag
     * @return Entities\ArticleEntity[]
     */
    public function getAllByTag(Entities\TagEntity $tag)
    {
        return $this->dataAccess->getAllByTag(Entities\ArticleEntity::class, $tag);
    }

    /**
     * @param  Entities\TagEntity          $tag
     * @param  string                      $name
     * @return Entities\ArticleEntity|null
     */
    public function getByTagAndName(Entities\TagEntity $tag, $name)
    {
        return $this->dataAccess->getByTagAndName(Entities\ArticleEntity::class, $tag, $name);
    }

    /**
     * @param  Entities\TagEntity          $tag
     * @param  string                      $slug
     * @return Entities\ArticleEntity|null
     */
    public function getByTagAndSlug(Entities\TagEntity $tag, $slug)
    {
        return $this->dataAccess->getByTagAndSlug(Entities\ArticleEntity::class, $tag, $slug);
    }

    /**
     * @param  int                $page
     * @param  int                $limit
     * @param  Entities\TagEntity $tag
     * @param  bool               $activeOnly
     * @return Paginator
     */
    public function getAllByTagForPage($page, $limit, Entities\TagEntity $tag, $activeOnly = false)
    {
        return $this->dataAccess->getAllByTagForPage(Entities\ArticleEntity::class, $page, $limit, $tag, $activeOnly);
    }

    /**
     * @param  int                 $page
     * @param  int                 $limit
     * @param  Entities\UserEntity $user
     * @return Paginator
     */
    public function getAllByUserForPage($page, $limit, Entities\UserEntity $user)
    {
        return $this->dataAccess->getAllByUserForPage(Entities\ArticleEntity::class, $page, $limit, $user);
    }

    /**
     * @return Entities\ArticleEntity[]
     */
    public function getAllActive()
    {
        return $this->dataAccess->getAllActive(Entities\ArticleEntity::class);
    }

    /**
     * @return Entities\ArticleEntity[]
     */
    public function getAllInactive()
    {
        return $this->dataAccess->getAllInactive(Entities\ArticleEntity::class);
    }

    /**
     * @param  int       $page
     * @param  int       $limit
     * @return Paginator
     */
    public function getAllInactiveForPage($page, $limit)
    {
        return $this->dataAccess->getAllInactiveForPage(Entities\ArticleEntity::class, $page, $limit);
    }

    /**
     * @param  int                $page
     * @param  int                $limit
     * @param  Entities\TagEntity $tag
     * @return Paginator
     */
    public function getAllInactiveByTagForPage($page, $limit, Entities\TagEntity $tag)
    {
        return $this->dataAccess->getAllInactiveByTagForPage(Entities\ArticleEntity::class, $page, $limit, $tag);
    }

    /**
     * @param  Entities\TagEntity       $tag
     * @return Entities\ArticleEntity[]
     */
    public function getAllActiveByTag(Entities\TagEntity $tag)
    {
        return $this->dataAccess->getAllActiveByTag(Entities\ArticleEntity::class, $tag);
    }

    /**
     * @return Entities\TagEntity[]
     */
    public function getAllTags()
    {
        return $this->dataAccess->getAllTags(Entities\ArticleEntity::class);
    }
}

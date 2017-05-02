<?php

namespace App\Caching;

use App\Dao\SingleUserContentDao;
use App\Dao\WikiDao;
use App\Entities;
use App\Repositories;
use Nette\Caching\Cache;

class TagCache
{
    /** @var Cache */
    private $cache;

    /** @var Repositories\TagRepository */
    private $tagRepository;

    public function __construct(Cache $cache, Repositories\TagRepository $tagRepository)
    {
        $this->cache         = $cache;
        $this->tagRepository = $tagRepository;
    }

    /**
     * @return Repositories\TagRepository
     */
    public function getTagRepository()
    {
        return $this->tagRepository;
    }

    /**
     * @param  int                $section
     * @param  Entities\TagEntity $tag
     * @return bool
     */
    public function isTagInSection($section, Entities\TagEntity $tag)
    {
        return array_key_exists($tag->id, $this->cache->load($section));
    }

    /**
     * @param int                $section
     * @param Entities\TagEntity $tag
     */
    public function deleteSectionIfTagNotPresent($section, Entities\TagEntity $tag)
    {
        if ($this->isTagInSection($section, $tag) === false) {
            $this->deleteSection($section);
        }
    }

    /**
     * @param int $section
     */
    public function deleteSection($section)
    {
        $this->cache->remove($section);
    }

    /**
     * @param  int                          $section
     * @param  Entities\TagEntity[]         $tags
     * @param  SingleUserContentDao|WikiDao $dao
     * @param  string                       $wikiType
     * @return array
     */
    public function getItemsForSection($section, array $tags, $dao, $wikiType = null)
    {
        $items = $this->cache->load($section);
        if ($items === null) {
            $items = [];
            foreach ($tags as $tag) {
                if ($wikiType ? $dao->getAllByTag($tag, $wikiType) : $dao->getAllByTag($tag)) {
                    $items[$tag->id] = $tag;
                }
            }
            $this->cache->save($section, $items);
        }

        return $items;
    }
}
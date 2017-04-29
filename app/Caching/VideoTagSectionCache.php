<?php

namespace App\Caching;

use App\Dao\SingleUserContentDao;
use App\Entities;

class VideoTagSectionCache implements TagSectionCacheInterface
{
    /** @var int */
    const SECTION_ID = 3;

    /** @var TagCache */
    private $tagCache;

    /** @var SingleUserContentDao */
    private $dataAccess;

    public function __construct(TagCache $tagCache, SingleUserContentDao $dataAccess)
    {
        $this->tagCache   = $tagCache;
        $this->dataAccess = $dataAccess;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tagCache->getItemsForSection(
            self::SECTION_ID,
            $this->tagCache->getTagRepository()->getAll(),
            $this->dataAccess
        );
    }

    /**
     * @param  Entities\TagEntity $tag
     * @return bool
     */
    public function isTagInSection(Entities\TagEntity $tag)
    {
        return $this->tagCache->isTagInSection(self::SECTION_ID, $tag);
    }

    /**
     * @param Entities\TagEntity $tag
     */
    public function deleteSectionIfTagNotPresent(Entities\TagEntity $tag)
    {
        $this->tagCache->deleteSectionIfTagNotPresent(self::SECTION_ID, $tag);
    }

    public function deleteSection()
    {
        $this->tagCache->deleteSection(self::SECTION_ID);
    }
}

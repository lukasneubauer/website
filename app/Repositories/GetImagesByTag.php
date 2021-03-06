<?php

namespace App\Repositories;

use App\Dao\SingleUserContentDao;
use App\Entities;

class GetImagesByTag implements AccessibleByTagInterface
{
    /** @var SingleUserContentDao */
    private $dataAccess;

    public function __construct(SingleUserContentDao $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    /**
     * @param  Entities\TagEntity     $tag
     * @return Entities\ImageEntity[]
     */
    public function getAllByTag(Entities\TagEntity $tag)
    {
        return $this->dataAccess->getAllByTag(Entities\ImageEntity::class, $tag);
    }
}

<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity
 * @ORM\Table(name="`video`")
 */
class VideoEntity extends BaseEntity
{
    use Identifier;
    use Timestampable;

    /** @var string */
    const TYPE_YOUTUBE = 'youtube';
    /** @var string */
    const TYPE_VIMEO = 'vimeo';

    /**
     * @ORM\ManyToOne(targetEntity="TagEntity")
     *
     * @var TagEntity
     */
    protected $tag;

    /**
     * @ORM\ManyToOne(targetEntity="UserEntity")
     *
     * @var UserEntity
     */
    protected $user;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $youtubeVideoSrc;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $youtubeVideoUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $vimeoVideoSrc;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $vimeoVideoUrl;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $isActive = false;
}

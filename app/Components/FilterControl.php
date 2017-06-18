<?php

namespace App\Components;

use App\Security\AccessChecker;
use Nette\Application\UI\Control;

class FilterControl extends Control
{
    /** @var string */
    const DISPLAY_DEFAULT = 'display-default';
    /** @var string */
    const DISPLAY_INACTIVE = 'display-inactive';
    /** @var string */
    const DISPLAY_DRAFTS = 'display-drafts';

    /** @var string @persistent */
    public $filter = self::DISPLAY_DEFAULT;

    /** @var AccessChecker */
    private $accessChecker;

    public function __construct(AccessChecker $accessChecker)
    {
        parent::__construct();

        $this->accessChecker = $accessChecker;
    }

    /**
     * @return bool
     */
    public function displayDefault()
    {
        return $this->filter === self::DISPLAY_DEFAULT;
    }

    /**
     * @return bool
     */
    public function displayInactive()
    {
        return $this->filter === self::DISPLAY_INACTIVE;
    }

    /**
     * @return bool
     */
    public function displayDrafts()
    {
        return $this->filter === self::DISPLAY_DRAFTS;
    }

    public function render()
    {
        $this->doRender(__DIR__ . '/templates/FilterControl.latte');
    }

    public function renderWithDrafts()
    {
        $this->doRender(__DIR__ . '/templates/FilterControl.withDrafts.latte');
    }

    /**
     * @param string $templateFile
     */
    private function doRender($templateFile)
    {
        $template = $this->getTemplate();

        $template->canAccess = $this->accessChecker->canAccess();

        $template->activeFilter = $this->filter;

        $template->setFile($templateFile);

        $template->render();
    }
}

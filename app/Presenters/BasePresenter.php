<?php

namespace App\Presenters;

use App\Entities;
use App\Forms\ExtendingMethods as FormExtendingMethods;
use App\Repositories;
use App\Security\AccessChecker;
use App\Security\LoggedUser;
use App\Utils\FlashType;
use Nette;
use Nette\Localization\ITranslator;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var ITranslator @inject */
    public $translator;

    /** @var AccessChecker @inject */
    public $accessChecker;

    /** @var LoggedUser @inject */
    public $loggedUser;

    protected function throw404()
    {
        $this->error($this->translator->translate('locale.error.page_not_found'));
    }

    /**
     * @param string $message
     * @param string $redirect
     */
    protected function flashWithRedirect($message = '', $redirect = 'this')
    {
        $this->flashMessage($message);
        $this->redirect($redirect);
    }

    /**
     * @param string $message
     * @param string $type
     * @param string $redirect
     */
    protected function flashTypeWithRedirect($message = '', $type = FlashType::INFO, $redirect = 'this')
    {
        $this->flashMessage($message, $type);
        $this->redirect($redirect);
    }

    /**
     * @param  int                         $itemId
     * @param  Repositories\BaseRepository $repository
     * @return Entities\BaseEntity|null
     */
    protected function getItem($itemId, Repositories\BaseRepository $repository)
    {
        return $itemId ? $repository->getById($itemId) : null;
    }

    protected function registerFormExtendingMethods()
    {
        $ext = new FormExtendingMethods;
        $ext->registerMethods();
    }
}

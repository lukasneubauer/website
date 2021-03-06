<?php

namespace App\Admin;

use App\Forms;
use App\Repositories;
use App\Videos\VideoThumbnail;

final class VideoPresenter extends SingleUserContentPresenter
{
    /** @var Repositories\VideoRepository @inject */
    public $videoRepository;

    /** @var Forms\VideoFormInterface @inject */
    public $videoForm;

    /** @var VideoThumbnail @inject */
    public $videoThumbnail;

    /**
     * @param int $id
     */
    public function actionForm($id = null)
    {
        $this->runActionForm($this->videoRepository, 'Video:default', $id);
    }

    public function renderForm()
    {
        $this->template->item = $this->item;
    }

    public function actionDefault()
    {
        $this->runActionDefault($this->videoRepository, 50, $this->loggedUser->getLoggedUserEntity());
    }

    public function renderDefault()
    {
        parent::renderDefault();

        $this->template->videoThumbnail = $this->videoThumbnail;
    }

    /**
     * @param int $id
     */
    public function actionDetail($id)
    {
        $item = $this->getItem($id, $this->videoRepository);

        $this->checkItemAndFlashWithRedirectIfNull($item, 'Video:default');

        $this->item = $item;
    }

    public function renderDetail()
    {
        $this->template->item = $this->item;
    }

    /**
     * @param int $videoId
     */
    public function handleActivate($videoId)
    {
        $this->runHandleActivate($videoId, $this->videoRepository);
    }

    /**
     * @param int $videoId
     */
    public function handleDelete($videoId)
    {
        $this->runHandleDelete($videoId, $this->videoRepository);
    }

    /**
     * @return Forms\VideoForm
     */
    protected function createComponentForm()
    {
        return $this->videoForm->create(
            $this->loggedUser->getLoggedUserEntity(),
            $this->item
        );
    }
}

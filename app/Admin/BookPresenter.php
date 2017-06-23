<?php

namespace App\Admin;

use App\Entities;
use App\Forms;

final class BookPresenter extends SharedContentPresenter
{
    /**
     * @param int $id
     */
    public function actionForm($id = null)
    {
        $this->runActionForm(Entities\WikiEntity::TYPE_BOOK, 'Book:default', $id);
    }

    public function actionDefault()
    {
        $this->runActionDefault(10, Entities\WikiEntity::TYPE_BOOK);
    }

    /**
     * @param int $id
     */
    public function actionDetail($id)
    {
        $item = $this->getItem($id, $this->wikiRepository);

        $this->checkItemAndFlashWithRedirectIfNull($item, 'Book:default');

        $this->item = $item;
    }

    public function renderDetail()
    {
        $this->template->item = $this->item;
    }

    /**
     * @param int $wikiId
     */
    public function handleActivate($wikiId)
    {
        $this->runHandleActivate($wikiId, $this->wikiRepository);
    }

    /**
     * @param int $wikiId
     */
    public function handleDelete($wikiId)
    {
        $this->runHandleDelete($wikiId, $this->wikiRepository);
    }

    /**
     * @return Forms\WikiForm
     */
    protected function createComponentWikiForm()
    {
        return $this->runCreateComponentWikiForm(Entities\WikiEntity::TYPE_BOOK);
    }

    /**
     * @return Forms\WikiDraftForm
     */
    protected function createComponentWikiDraftForm()
    {
        return $this->runCreateComponentWikiDraftForm(Entities\WikiEntity::TYPE_BOOK);
    }
}
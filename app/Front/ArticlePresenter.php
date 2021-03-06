<?php

namespace App\Front;

use App\Caching;
use App\Components;
use App\Entities;
use App\Repositories;

final class ArticlePresenter extends SingleUserContentPresenter
{
    /** @var Components\TagsControlInterface @inject */
    public $tagsControl;

    /** @var Caching\ArticleTagSectionCache @inject */
    public $articleTagSectionCache;

    /** @var Repositories\ArticleRepository @inject */
    public $articleRepository;

    /** @var Entities\ArticleEntity[] */
    private $articles;

    /** @var Entities\ArticleEntity */
    private $article;

    /**
     * @param string $tagSlug
     */
    public function actionDefault($tagSlug)
    {
        $this->articles = $this->runActionDefault($this->articleRepository, $tagSlug, 10);
    }

    public function renderDefault()
    {
        parent::runRenderDefault();

        $this->template->articles = $this->articles;
    }

    /**
     * @param string $tagSlug
     * @param string $slug
     */
    public function actionDetail($tagSlug, $slug)
    {
        $this->checkBacklinks();

        $tag = $this->getTag($tagSlug);

        $this->throw404IfNoTagOrSlug($tag, $slug);

        $article = $this->articleRepository->getByTagAndSlug($tag, $slug);

        if ((!$article || !$article->isActive) && !$this->accessChecker->canAccess()) {
            $this->throw404();
        }

        $this->article = $article;
    }

    public function renderDetail()
    {
        $this->template->article = $this->article;
    }

    /**
     * @param int $articleId
     */
    public function handleActivate($articleId)
    {
        $article = $this->getItem($articleId, $this->articleRepository);

        if (!$article) {
            $this->throw404();
        }

        $this->articleRepository->activate($article);

        $this->flashWithRedirect($this->translator->translate('locale.item.activated'));
    }

    /**
     * @param int $articleId
     */
    public function handleDelete($articleId)
    {
        $article = $this->getItem($articleId, $this->articleRepository);

        if (!$article) {
            $this->throw404();
        }

        $this->articleRepository->delete($article);

        $this->flashWithRedirect($this->translator->translate('locale.item.deleted'));
    }

    /**
     * @return Components\TagsControlInterface
     */
    protected function createComponentTagsControl()
    {
        return $this->tagsControl->create($this->articleTagSectionCache);
    }
}

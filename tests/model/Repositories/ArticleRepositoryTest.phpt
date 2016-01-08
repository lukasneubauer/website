<?php

namespace AppTests\Model\Repositories;

use App\Model\Entities as AppEntities;
use App\Model\Repositories as AppRepositories;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Mockery as m;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class ArticleRepositoryTest extends Tester\TestCase
{
    private $dao;
    private $em;
    private $qb;
    private $query;
    private $translator;

    private function getEntityDaoMock()
    {
        return m::mock('Kdyby\Doctrine\EntityDao');
    }

    private function getEntityManagerMock()
    {
        return m::mock('Kdyby\Doctrine\EntityManager');
    }

    private function getQueryBuilderMock()
    {
        return m::mock('Kdyby\Doctrine\QueryBuilder');
    }

    private function getQueryMock()
    {
        return m::mock('Doctrine\ORM\AbstractQuery');
    }

    protected function getTranslatorMock()
    {
        return m::mock('Nette\Localization\ITranslator');
    }

    protected function setUp()
    {
        $this->dao        = $this->getEntityDaoMock();
        $this->em         = $this->getEntityManagerMock();
        $this->qb         = $this->getQueryBuilderMock();
        $this->query      = $this->getQueryMock();
        $this->translator = $this->getTranslatorMock();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetAllForPage()
    {
        $query = $this->query;

        $qb = $this->qb;
        $qb->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setFirstResult')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setMaxResults')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('getQuery')
            ->once()
            ->andReturn($query);

        $dao = $this->dao;
        $dao->shouldReceive('createQueryBuilder')
            ->once()
            ->andReturn($qb);

        $repo = new AppRepositories\ArticleRepository(
            $dao,
            $this->translator,
            $this->em
        );

        Assert::true($repo->getAllForPage(1, 10) instanceof Paginator);
    }

    public function testGetAllByTag()
    {
        $query = $this->query;
        $query->shouldReceive('getResult')
            ->once()
            ->andReturn(array());

        $qb = $this->qb;
        $qb->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('join')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('where')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setParameter')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('getQuery')
            ->once()
            ->andReturn($query);

        $dao = $this->dao;
        $dao->shouldReceive('createQueryBuilder')
            ->once()
            ->andReturn($qb);

        $repo = new AppRepositories\ArticleRepository(
            $dao,
            $this->translator,
            $this->em
        );

        Assert::type('array', $repo->getAllByTag(new AppEntities\TagEntity));
    }

    public function testGetByTagAndName()
    {
        $repo = $this->prepareRepositoryForDetail();

        Assert::true($repo->getByTagAndName(new AppEntities\TagEntity, 'Silent Hill') instanceof AppEntities\ArticleEntity);
    }

    public function testGetByTagAndSlug()
    {
        $repo = $this->prepareRepositoryForDetail();

        Assert::true($repo->getByTagAndSlug(new AppEntities\TagEntity, 'silent-hill') instanceof AppEntities\ArticleEntity);
    }

    private function prepareRepositoryForDetail()
    {
        $query = $this->query;
        $query->shouldReceive('getSingleResult')
            ->once()
            ->andReturn(new AppEntities\ArticleEntity);

        $qb = $this->qb;
        $qb->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('join')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('where')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setParameters')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('getQuery')
            ->once()
            ->andReturn($query);

        $dao = $this->dao;
        $dao->shouldReceive('createQueryBuilder')
            ->once()
            ->andReturn($qb);

        return new AppRepositories\ArticleRepository(
            $dao,
            $this->translator,
            $this->em
        );
    }

    public function testGetAllByTagForPage()
    {
        $query = $this->query;

        $qb = $this->qb;
        $qb->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('join')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('where')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setParameters')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setFirstResult')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setMaxResults')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('getQuery')
            ->once()
            ->andReturn($query);

        $dao = $this->dao;
        $dao->shouldReceive('createQueryBuilder')
            ->once()
            ->andReturn($qb);

        $repo = new AppRepositories\ArticleRepository(
            $dao,
            $this->translator,
            $this->em
        );

        Assert::true($repo->getAllByTagForPage(1, 10, new AppEntities\TagEntity) instanceof Paginator);
    }

    public function testGetAllByUserForPage()
    {
        $query = $this->query;

        $qb = $this->qb;
        $qb->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('join')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('where')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setParameter')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setFirstResult')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setMaxResults')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('getQuery')
            ->once()
            ->andReturn($query);

        $dao = $this->dao;
        $dao->shouldReceive('createQueryBuilder')
            ->once()
            ->andReturn($qb);

        $repo = new AppRepositories\ArticleRepository(
            $dao,
            $this->translator,
            $this->em
        );

        Assert::true($repo->getAllByUserForPage(1, 10, new AppEntities\UserEntity) instanceof Paginator);
    }
}

$testCase = new ArticleRepositoryTest;
$testCase->run();

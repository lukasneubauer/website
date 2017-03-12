<?php

namespace AppTests\Unit\Videos;

use AppTests\UnitMocks;
use App\Utils\PaginatorFactory;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class PaginatorFactoryTest extends Tester\TestCase
{
    use UnitMocks;

    public function testCreatePaginator()
    {
        Assert::type('Doctrine\ORM\Tools\Pagination\Paginator', (new PaginatorFactory)->createPaginator($this->query));
    }
}

$testCase = new PaginatorFactoryTest;
$testCase->run();
<?php

namespace AppTests\Integration\Admin;

use AppTests;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class GameListingPresenterTest extends Tester\TestCase
{
    use AppTests\Fixtures;
    use AppTests\Login;
    use AppTests\PresenterTester;

    public function testActionDefault()
    {
        $this->applyFixtures($this->container, __DIR__ . '/fixtures.php');
        $this->signIn($this->container);
        $this->assertAppResponse('Admin:GameListing', 'default', 'GET');
    }
}

$testCase = new GameListingPresenterTest($container);
$testCase->run();

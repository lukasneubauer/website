<?php

namespace AppTests\Integration\Front;

use AppTests\PresenterTester;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class BookPresenterTest extends Tester\TestCase
{
    use PresenterTester;

    public function testActionDefault()
    {
        $this->assertAppResponse('Front:Book', 'default', 'GET');
    }
}

$testCase = new BookPresenterTest($container);
$testCase->run();

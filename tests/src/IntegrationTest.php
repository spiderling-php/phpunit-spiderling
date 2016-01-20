<?php

namespace SP\PhpunitSpiderling\Test;

use SP\Phpunit\SpiderlingTestCase;
use SP\Phpunit\SnapshotTestInterface;
use SP\Phpunit\SnapshotTestTrait;
use SP\PhantomDriver;
use SP\Spiderling\BrowserSession;
use Symfony\Component\Process\Process;

/**
 * @coversDefaultClass SP\PhpunitSpiderling\Integration
 */
class IntegrationTest extends SpiderlingTestCase
{
    public function setUp()
    {
        $server = new Process('php -S 127.0.0.1:4295', __DIR__.'/../html');
        $server->start();

        $this->addSession('phantomjs', function () {
            $server = new PhantomDriver\Server();
            $server->start()->wait();

            return new BrowserSession(new PhantomDriver\Browser($server->getClient()));
        });

        $this->addFailureAction(function ($filename) {
            $this->saveSessionSnapshot($filename, __DIR__.'/../log', 'http://127.0.0.1:4295');
        });

        parent::setUp();
    }

    public function testLogOnFailure()
    {
        $session = $this->getSession('phantomjs');

        $session
            ->open('http://127.0.0.1:4295')
            ->get('h1');

        $this->saveSessionSnapshot('SP_PhpunitSpiderling_Test_IntegrationTest::testLogOnFailure', __DIR__.'/../log', 'http://127.0.0.1:4295');

        $this->assertFileExists(__DIR__.'/../log/SP_PhpunitSpiderling_Test_IntegrationTest::testLogOnFailure.html');
        $this->assertFileExists(__DIR__.'/../log/SP_PhpunitSpiderling_Test_IntegrationTest::testLogOnFailure.png');

        $html = file_get_contents(__DIR__.'/../log/SP_PhpunitSpiderling_Test_IntegrationTest::testLogOnFailure.html');

        $this->assertContains('<img src="http://127.0.0.1:4295/icon3.png" width="16" height="16" alt="icon 3">', $html);
        $this->assertContains('href="http://127.0.0.1:4295/other.html', $html);
        $this->assertContains('<form action="http://127.0.0.1:4295/submit.php"', $html);

        unlink(__DIR__.'/../log/SP_PhpunitSpiderling_Test_IntegrationTest::testLogOnFailure.html');
        unlink(__DIR__.'/../log/SP_PhpunitSpiderling_Test_IntegrationTest::testLogOnFailure.png');
    }
}

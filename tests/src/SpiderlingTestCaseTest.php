<?php

namespace SP\PhpunitSpiderling\Test;

use SP\Phpunit\SpiderlingTestCase;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass SP\Phpunit\SpiderlingTestCase
 */
class SpiderlingTestCaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getSessionContainer
     */
    public function testGetSessionContainer()
    {
        $sessions = SpiderlingTestCase::getSessionContainer();

        $this->assertInstanceOf('SP\Phpunit\SessionContainer', $sessions);

        $this->assertSame(
            $sessions,
            SpiderlingTestCase::getSessionContainer(),
            'Should return the same object'
        );
    }

    /**
     * @covers ::getSession
     * @covers ::getSessionContainer
     */
    public function testGetSession()
    {
        $built = null;

        SpiderlingTestCase::getSessionContainer()->addBuilder('testGetSession', function () use (& $built) {
            $built = $this
                ->getMockBuilder('SP\Spiderling\BrowserSession')
                ->disableOriginalConstructor()
                ->getMock();

            return $built;
        });

        $test = new SpiderlingTestCase();

        $session = $test->getSession('testGetSession');

        $this->assertSame($built, $session);

        $this->assertSame(
            $session,
            $test->getSession('testGetSession'),
            'Should return the same object'
        );
    }

    /**
     * @covers ::getSession
     */
    public function testGetSessionNoBuilder()
    {
        $test = new SpiderlingTestCase();

        $this->setExpectedException(
            'InvalidArgumentException',
            'SpiderlingTestCase::getSessions()->addBuilder("testGetSessionNoBuilder", function () { ... });'
        );

        $test->getSession('testGetSessionNoBuilder');
    }

    /**
     * @covers ::setCurrentSession
     * @covers ::getCurrentSession
     * @covers ::hasCurrentSession
     */
    public function testCurrentSession()
    {
        $session = $this
            ->getMockBuilder('SP\Spiderling\BrowserSession')
            ->disableOriginalConstructor()
            ->getMock();

        $test = new SpiderlingTestCase();

        $this->assertFalse($test->hasCurrentSession());

        $test->setCurrentSession($session);

        $this->assertTrue($test->hasCurrentSession());

        $this->assertSame($session, $test->getCurrentSession());
    }

    /**
     * @covers ::saveSessionSnapshot
     */
    public function testSaveSessionSnapshot()
    {
        $session =  $this
            ->getMockBuilder('SP\Spiderling\BrowserSession')
            ->disableOriginalConstructor()
            ->getMock();

        $test = new SpiderlingTestCase();

        $test->setCurrentSession($session);

        $session
            ->expects($this->once())
            ->method('saveScreenshot')
            ->with(__DIR__.'/unique.png');

        $session
            ->expects($this->once())
            ->method('saveHtml')
            ->with(__DIR__.'/unique.html', 'http://127.0.0.1:4295');

        $test->saveSessionSnapshot('unique', __DIR__, 'http://127.0.0.1:4295');
    }

    /**
     * @covers ::saveSessionSnapshot
     */
    public function testSaveSessionSnapshotNoDir()
    {
        $test = new SpiderlingTestCase();

        $this->setExpectedException(
            'InvalidArgumentException',
            'phpunit-spiderling/tests/src/unknown-dir exists and is writable'
        );

        $test->saveSessionSnapshot('unique', __DIR__.'/unknown-dir', 'http://127.0.0.1:4295');
    }
}

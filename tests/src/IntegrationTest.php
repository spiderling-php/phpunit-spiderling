<?php

namespace SP\PhpunitSpiderling\Test;

use SP\Phpunit\SpiderlingTestCase;

/**
 * @coversDefaultClass SP\Phpunit\SpiderlingTestCase
 */
class IntegrationTest extends SpiderlingTestCase
{
    /**
     * @covers ::getSessionContainer
     */
    public function testLogOnFailure()
    {
        self::getSessionContainer()->addBuilder('test', function () {
            return $this
                ->getMockBuilder('SP\Spiderling\BrowserSession')
                ->disableOriginalConstructor()
                ->getMock();
        });

        $session = $this->getSession('test');

        $session
            ->expects($this->once())
            ->method('saveScreenshot')
            ->with(__DIR__.'/unique.png');

        $session
            ->expects($this->once())
            ->method('saveHtml')
            ->with(__DIR__.'/unique.html', 'http://127.0.0.1:4295');

        $this->saveSessionSnapshot('unique', __DIR__, 'http://127.0.0.1:4295');
    }
}

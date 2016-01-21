<?php

namespace SP\PhpunitSpiderling\Test;

use SP\Phpunit\SessionContainer;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass SP\Phpunit\SessionContainer
 */
class SessionContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::addBuilder
     * @covers ::getBuilder
     */
    public function testBuilders()
    {
        $container = new SessionContainer();

        $builder = function () {
            return '1';
        };

        $container->addBuilder('test', $builder);

        $this->assertSame($builder, $container->getBuilder('test'));
    }

    /**
     * @covers ::getBuilder
     */
    public function testBuildersEmpty()
    {
        $container = new SessionContainer();

        $this->setExpectedException(
            'InvalidArgumentException',
            'SpiderlingTestCase::getSessions()->addBuilder("test", function () { ... });'
        );

        $container->getBuilder('test');
    }

    /**
     * @covers ::set
     * @covers ::has
     * @covers ::get
     */
    public function testGettersSetters()
    {
        $container = new SessionContainer();

        $session = $this
            ->getMockBuilder('SP\Spiderling\BrowserSession')
            ->disableOriginalConstructor()
            ->getMock();

        $dummy = $this
            ->getMockBuilder('stdClass')
            ->setMethods(['builder'])
            ->getMock();

        $dummy
            ->expects($this->once())
            ->method('builder')
            ->willReturn($session);

        $container->addBuilder('test', [$dummy, 'builder']);

        $this->assertFalse($container->has('test'));
        $this->assertSame($session, $container->get('test'));

        $this->assertTrue(
            $container->has('test'),
            'Should report that "test" session exists after "get" is called'
        );

        $this->assertSame(
            $session,
            $container->get('test'),
            'Should report on "test"'
        );

        $session2 = $this
            ->getMockBuilder('SP\Spiderling\BrowserSession')
            ->disableOriginalConstructor()
            ->getMock();

        $container->set('other', $session2);

        $this->assertTrue($container->has('other'));
        $this->assertSame($session2, $container->get('other'));
    }
}

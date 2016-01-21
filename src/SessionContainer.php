<?php

namespace SP\Phpunit;

use PHPUnit_Framework_TestCase;
use SP\Spiderling\CrawlerSession;
use SP\Spiderling\BrowserSession;
use SP\Phpunit\FailureActionInterface;
use SP\Phpunit\FailureActionTrait;
use InvalidArgumentException;

class SessionContainer
{
    /**
     * @var callable[]
     */
    private $builders;

    /**
     * @var CrawlerSession[]
     */
    private $sessions;

    /**
     * Add a session builder, to be executed once to retrieve a session object
     *
     * @param string   $name
     * @param callable $function
     */
    public function addBuilder($name, callable $function)
    {
        $this->builders[$name] = $function;
    }

    public function getBuilder($name)
    {
        if (empty($this->builders[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Session %s doesn\'t have a builder. Add it with SpiderlingTestCase::getSessions()->addBuilder("%s", function () { ... });',
                $name,
                $name
            ));
        }

        return $this->builders[$name];
    }

    public function set($name, CrawlerSession $session)
    {
        $this->sessions[$name] = $session;
    }

    public function has($name)
    {
        return isset($this->sessions[$name]);
    }

    public function get($name)
    {
        if (false === $this->has($name)) {
            $this->set($name, call_user_func($this->getBuilder($name)));
        }

        return $this->sessions[$name];
    }
}

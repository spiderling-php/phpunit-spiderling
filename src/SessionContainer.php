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

    /**
     * @param  string $name
     * @return callable
     */
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

    /**
     * @param string         $name
     * @param CrawlerSession $session
     */
    public function set($name, CrawlerSession $session)
    {
        $this->sessions[$name] = $session;
    }

    /**
     * @param  string  $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->sessions[$name]);
    }

    /**
     * @param  string $name
     * @throws InvalidArgumentException if no builder with that name was found
     * @return CrawlerSession|BrowserSession
     */
    public function get($name)
    {
        if (false === $this->has($name)) {
            $this->set($name, call_user_func($this->getBuilder($name)));
        }

        return $this->sessions[$name];
    }
}

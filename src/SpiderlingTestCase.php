<?php

namespace SP\Phpunit;

use PHPUnit_Framework_TestCase;
use SP\Spiderling\CrawlerSession;
use SP\Spiderling\BrowserSession;
use SP\Phpunit\FailureActionInterface;
use SP\Phpunit\FailureActionTrait;

class SpiderlingTestCase extends PHPUnit_Framework_TestCase implements FailureActionInterface
{
    use FailureActionTrait;

    private $builders;
    private $session;
    private $logDir;
    private $base = null;

    public function addSession($name, callable $function)
    {
        $this->builders[$name] = $function;
    }

    public function getSession($name)
    {
        if (empty($this->session)) {
            $this->session = call_user_func($this->builders[$name]);
        }

        return $this->session;
    }

    public function hasSession()
    {
        return (bool) $this->session;
    }

    public function saveSessionSnapshot($filename, $logDir, $base)
    {
        if ($this->session instanceof CrawlerSession) {
            $this->session->saveHtml("$logDir/$filename.html", $base);
        }

        if ($this->session instanceof BrowserSession) {
            $this->session->saveScreenshot("$logDir/$filename.png");
        }
    }
}

<?php

namespace SP\Phpunit;

use PHPUnit_Framework_TestCase;
use SP\Spiderling\CrawlerSession;
use SP\Spiderling\BrowserSession;
use SP\Phpunit\FailureActionInterface;
use SP\Phpunit\FailureActionTrait;
use InvalidArgumentException;

class SpiderlingTestCase extends PHPUnit_Framework_TestCase implements FailureActionInterface
{
    use FailureActionTrait;

    /**
     * @var SessionContainer
     */
    private static $sessionContainer;

    public static function getSessionContainer()
    {
        if (null === self::$sessionContainer) {
            self::$sessionContainer = new SessionContainer();
        }

        return self::$sessionContainer;
    }

    /**
     * @var CrawlerSession|BrowserSession
     */
    private $currentSession;

    /**
     * This set the current session directly
     *
     * @param CrawlerSession $session
     */
    public function setCurrentSession(CrawlerSession $session)
    {
        $this->currentSession = $session;
    }

    /**
     * @param  string $name
     * @throws InvalidArgumentException if no builder was found for the session name
     * @return CrawlerSession|BrowserSession
     */
    public function getSession($name)
    {
        $this->currentSession = self::getSessionContainer()->get($name);

        return $this->currentSession;
    }

    /**
     * @return CrawlerSession|BrowserSession|null
     */
    public function getCurrentSession()
    {
        return $this->currentSession;
    }

    /**
     * Is there a currently assigned session
     *
     * @return boolean
     */
    public function hasCurrentSession()
    {
        return (bool) $this->currentSession;
    }

    /**
     * @param  string                   $filename Snapshot filename. Should be unique per test
     * @param  string                   $logDir   Directory to write snapshots into
     * @param  UriInterface|string|null $base     Resolve all relative links with this base uri
     */
    public function saveSessionSnapshot($filename, $logDir, $base = null)
    {
        if (is_dir($logDir) && is_writable($logDir)) {
            if ($this->currentSession instanceof CrawlerSession) {
                $this->currentSession->saveHtml("$logDir/$filename.html", $base);
            }

            if ($this->currentSession instanceof BrowserSession) {
                $this->currentSession->saveScreenshot("$logDir/$filename.png");
            }
        } else {
            throw new InvalidArgumentException(sprintf(
                'Make sure directory %s exists and is writable',
                $logDir
            ));
        }
    }
}

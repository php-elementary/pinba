<?php

namespace elementary\monitoring;

use elementary\core\Singleton\SingletonInterface;
use elementary\core\Singleton\SingletonTrait;
use elementary\monitoring\exceptions\TimerExistException;

class Timer implements SingletonInterface
{
    use SingletonTrait;

    /** @var string */
    protected $key;

    /**
     * @param array $tags Array of tags and their values in the form of "tag" => "value". Cannot contain numeric indexes for obvious reasons.
     *
     * @return $this
     */
    public function start(array $tags=[])
    {
        if ($this->isExists()) {
            throw new TimerExistException('Another Timer is allready started.');
        }

        $key = microtime();
        $this->getTimers()->start($key, $tags);
        $this->setKey($key);

        return $this;
    }

    /**
     * @return $this
     */
    public function stop()
    {
        $key = $this->getKey();
        $this->getTimers()->stop($key);
        $this->setKey(null);

        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        $key = $this->getKey();
        $this->getTimers()->delete($key);
        $this->setKey(null);

        return $this;
    }

    /**
     * @return array
     */
    public function info()
    {
        $key = $this->getKey();
        return $this->getTimers()->info($key);
    }

    /**
     * @return bool
     */
    public function isExists()
    {
        $key = $this->getKey();
        return $this->getTimers()->isExists($key);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return Timer
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return Timers
     */
    protected function getTimers()
    {
        return Timers::me();
    }
}
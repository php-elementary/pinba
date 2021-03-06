<?php

namespace elementary\monitoring;

use elementary\core\Singleton\SingletonInterface;
use elementary\core\Singleton\SingletonTrait;
use elementary\monitoring\exceptions\TimerExistException;
use elementary\monitoring\exceptions\TimerNotFoundException;

class Timers implements SingletonInterface
{
    use SingletonTrait;

    /** @var array */
    protected $timers = [];

    /**
     * @param array $tags Array of tags and their values in the form of "tag" => "value". Cannot contain numeric indexes for obvious reasons.
     * @param int   $value
     *
     * @return $this
     */
    public function add(array $tags=[], $value=0)
    {
        $this->pinba()->timerAdd($tags, $value);

        return $this;
    }

    /**
     * @param string $key The internal key is not sent to the Pinba server.
     * @param array  $tags Array of tags and their values in the form of "tag" => "value". Cannot contain numeric indexes for obvious reasons.
     *
     * @return $this
     * @throws TimerExistException
     */
    public function start($key, array $tags=[])
    {
        if ($this->isExists($key)) {
            throw new TimerExistException('The Timer is allready started: '. $key);
        }

        $this->setTimer($key, $this->pinba()->timerStart($tags));

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function stop($key)
    {
        $this->pinba()->timerStop($this->getTimer($key));
        $this->unsetTimer($key);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function delete($key)
    {
        $this->pinba()->timerDelete($this->getTimer($key));
        $this->unsetTimer($key);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function info($key)
    {
        return $this->pinba()->timerGetInfo($this->getTimer($key));
    }

    /**
     * @return array
     */
    protected function getTimers()
    {
        return $this->timers;
    }

    /**
     * @param string $key
     *
     * @return resource
     * @throws TimerNotFoundException
     */
    protected function getTimer($key)
    {
        if (!$this->isExists($key)) {
            throw new TimerNotFoundException('Timer not found: ' . $key);
        }

        return $this->timers[$key];
    }

    /**
     * @param string   $key
     * @param resource $value
     *
     * @return $this
     */
    protected function setTimer($key, $value)
    {
        $this->timers[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    protected function unsetTimer($key)
    {
        unset($this->timers[$key]);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isExists($key)
    {
        return array_key_exists($key, $this->getTimers());
    }

    public function pinba()
    {
        return Pinba::me();
    }
}

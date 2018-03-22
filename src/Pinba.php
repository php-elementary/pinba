<?php

namespace elementary\monitoring;

use elementary\core\Singleton\SingletonTrait;

class Pinba
{
    use SingletonTrait;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param array $tags
     *
     * @return bool|resource
     */
    public function timerStart(array $tags)
    {
        if ($this->isEnabled()) {
            return pinba_timer_start($tags);
        } else {
            return true;
        }
    }

    /**
     * @param array $tags
     * @param float $value
     *
     * @return bool|resource
     */
    public function timerAdd(array $tags, $value)
    {
        if ($this->isEnabled()) {
            return pinba_timer_add($tags, $value);
        } else {
            return true;
        }
    }

    /**
     * @param resource $timer
     *
     * @return bool
     */
    public function timerStop($timer)
    {
        if ($this->isEnabled()) {
            return pinba_timer_stop($timer);
        } else {
            return true;
        }
    }

    /**
     * @param resource $timer
     *
     * @return bool
     */
    public function timerDelete($timer)
    {
        if ($this->isEnabled()) {
            return pinba_timer_delete($timer);
        } else {
            return true;
        }
    }

    /**
     * @param resource $timer
     *
     * @return array
     */
    public function timerGetInfo($timer)
    {
        if ($this->isEnabled()) {
            return pinba_timer_get_info($timer);
        } else {
            return [];
        }
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setScriptName($name)
    {
        if ($this->isEnabled()) {
            pinba_script_name_set($name);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setServerName($name)
    {
        if ($this->isEnabled()) {
            pinba_server_name_set($name);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setHostName($name)
    {
        if ($this->isEnabled()) {
            pinba_hostname_set($name);
        }

        return $this;
    }

    /**
     * Ручная запись данных
     *
     * @return $this
     */
    public function flush()
    {
        if ($this->isEnabled()) {
            pinba_flush();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->getEnabled() === null) {
            $this->setEnabled(ini_get("pinba.enabled") === "1");
        }

        return $this->getEnabled();
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
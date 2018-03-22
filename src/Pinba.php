<?php

namespace elementary\monitoring;

use elementary\core\Singleton\SingletonTrait;

class Pinba
{
    use SingletonTrait;

    /**
     * Flush only stopped timers (by default all existing timers are stopped and flushed)
     * @since 1.0.0.
     */
    const PINBA_FLUSH_ONLY_STOPPED_TIMERS = 0;

    /**
     * Reset common request data
     * @since 1.1.0.
     */
    const PINBA_FLUSH_RESET_DATA = 1;

    /**
     * @since 1.0.0.
     */
    const PINBA_ONLY_STOPPED_TIMERS = 0;

    /** @var bool */
    protected $enabled;

    /** @var array */
    protected $pinboardTags = [];

    /**
     * Creates new timer.
     * This timer is already stopped and have specified time value.
     *
     * @param array $tags An array of tags and their values in the form of "tag" => "value". Cannot contain numeric indexes for obvious reasons.
     * @param float $value Timer value for new timer.
     *
     * @return null|resource Always returns new timer resource.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_add
     */
    public function timerAdd(array $tags, $value)
    {
        if ($this->isEnabled()) {
            $tags = array_merge($tags, $this->getPinboardTags());
            return pinba_timer_add($tags, $value);
        } else {
            return null;
        }
    }

    /**
     * Creates and starts new timer.
     *
     * @param array $tags An array of tags and their values in the form of "tag" => "value". Cannot contain numeric indexes for obvious reasons.
     *
     * @return null|resource New timer resource.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_start
     *
     * @example
     * <pre>
     * $time = pinba_timer_start(array('tag' => 'value'), array('customData', 1, 2));
     * </pre>
     */
    public function timerStart(array $tags)
    {
        if ($this->isEnabled()) {
            $tags = array_merge($tags, $this->getPinboardTags());
            return pinba_timer_start($tags);
        } else {
            return null;
        }
    }

    /**
     * Stops the timer.
     *
     * @param resource $timer Valid timer resource.
     *
     * @return bool Returns true on success and false on failure (if the timer has already been stopped).
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_stop
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
     * Stops all running timers.
     *
     * @return bool Returns true on success and false on failure.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timers_stop
     */
    public function timersStop()
    {
        if ($this->isEnabled()) {
            return pinba_timers_stop();
        } else {
            return true;
        }
    }

    /**
     * Deletes the timer.
     *
     * @param resource $timer Valid timer resource.
     *
     * @return bool Returns true on success and false on failure.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_delete
     * @since 0.0.6
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
     * Returns timer data.
     *
     * @param resource $timer Valid timer resource.
     *
     * @return array Array with timer data.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_get_info
     *
     * @example
     * <pre>
     * $data = pinba_timer_get_info($timerRes);
     * //$data == array(
     * //     "value" => 0.0213,
     * //     "tags" => array(
     * //         "foo" => "bar",
     * //     },
     * //      "started" => true,
     * //     "data"  => array('customData', 1, 2),
     * //);
     * </pre>
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
     * Get all timers info.
     *
     * @param int $flag Is an optional argument added in version 1.0.0.
     * Possible values (it's a bitmask, so you can add the constants) is a PINBA_ONLY_STOPPED_TIMERS.
     *
     * @return array Array with all timers data.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timers_get
     * @since 1.1.0
     */
    public function timersGet($flag = self::PINBA_ONLY_STOPPED_TIMERS)
    {
        if ($this->isEnabled()) {
            return pinba_timers_get($flag);
        } else {
            return [];
        }
    }

    /**
     * Merges $tags array with the timer tags replacing existing elements.
     *
     * @param resource $timer Valid timer resource
     * @param array $tags An array of tags and their values in the form of "tag" => "value". Cannot contain numeric indexes for obvious reasons.
     *
     * @return bool Returns true on success and false on failure.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_tags_merge
     */
    public function timerTagsMerge($timer, array $tags)
    {
        if ($this->isEnabled()) {
            return pinba_timer_tags_merge($timer, $tags);
        } else {
            return false;
        }
    }

    /**
     * Merges $data array with the timer user data replacing existing elements.
     *
     * @param resource $timer Valid timer resource
     * @param array $data An array of user data.
     *
     * @return bool Returns true on success and false on failure.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_data_merge
     */
    public function timerDataMerge($timer, array $data)
    {
        if ($this->isEnabled()) {
            return pinba_timer_data_merge($timer, $data);
        } else {
            return false;
        }
    }

    /**
     * Replaces timer tags with the passed $tags array.
     *
     * @param resource $timer Valid timer resource
     * @param array $tags An array of tags and their values in the form of "tag" => "value". Cannot contain numeric indexes for obvious reasons.
     *
     * @return bool Returns true on success and false on failure.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_tags_replace
     */
    public function timerTagsReplace($timer, array $tags)
    {
        if ($this->isEnabled()) {
            $tags = array_merge($tags, $this->getPinboardTags());
            return pinba_timer_tags_replace($timer, $tags);
        } else {
            return false;
        }
    }

    /**
     * Replaces timer user data with the passed $data array.
     * Use NULL value to reset user data in the timer.
     *
     * @param resource $timer Valid timer resource
     * @param array $data An array of user data.
     *
     * @return bool Returns true on success and false on failure.
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_timer_data_replace
     */
    public function timerDataReplace($timer, array $data)
    {
        if ($this->isEnabled()) {
            return pinba_timer_data_replace($timer, $data);
        } else {
            return false;
        }
    }

    /**
     * Returns all request data (including timers user data).
     *
     * @return array Requested data
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_get_info
     *
     * @example
     * <pre>
     * $data = pinba_get_info();
     * //$data == array(
     * //    "mem_peak_usage" => 786432,
     * //    "req_time" => 0.001529,
     * //    "ru_utime" => 0,
     * //    "ru_stime" => 0,
     * //    "req_count" => 1,
     * //    "doc_size" => 0,
     * //    "server_name" => "unknown",
     * //    "script_name" => "-",
     * //    "timers" =>array(
     * //        array(
     * //            "value" => 4.5E-5,
     * //            "tags" => array("foo" => "bar"),
     * //            "started" => true,
     * //            "data" => null,
     * //        ),
     * //    ),
     * //);
     * </pre>
     */
    public function getInfo()
    {
        if ($this->isEnabled()) {
            return pinba_get_info();
        } else {
            return [];
        }
    }

    /**
     * Set custom script name instead of $_SERVER['SCRIPT_NAME'] used by default.
     * Useful for those using front controllers, when all requests are served by one PHP script.
     *
     * @param string $scriptName Custom script name
     *
     * @return $this
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_script_name_set
     */
    public function setScriptName($scriptName)
    {
        if ($this->isEnabled()) {
            pinba_script_name_set($scriptName);
        }

        return $this;
    }

    /**
     * Set request schema (HTTP/HTTPS/whatever).
     *
     * @param string $schema Schema
     *
     * @return $this
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_schema_set
     * @since 1.1.0.
     */
    public function setSchema($schema)
    {
        if ($this->isEnabled()) {
            pinba_schema_set($schema);
        }

        return $this;
    }

    /**
     * Set custom server name instead of $_SERVER['SERVER_NAME'] used by default.
     *
     * @param string $serverName Custom server name
     *
     * @return $this
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_server_name_set
     * @since 1.1.0.
     */
    public function setServerName($serverName)
    {
        if ($this->isEnabled()) {
            pinba_server_name_set($serverName);
        }

        return $this;
    }

    /**
     * Set custom hostname instead of the result of gethostname() used by default.
     *
     * @param string $hostname Custom host name
     *
     * @return $this
     * @link https://github.com/tony2001/pinba_engine/wiki/PHP-extension#pinba_hostname_set
     */
    public function setHostName($hostname)
    {
        if ($this->isEnabled()) {
            pinba_hostname_set($hostname);
        }

        return $this;
    }

    /**
     * Set custom request time.
     *
     * @param float $requestTime
     *
     * @return $this
     */
    public function setRequestTime($requestTime)
    {
        if ($this->isEnabled()) {
            pinba_request_time_set($requestTime);
        }

        return $this;
    }

    /**
     * Useful when you need to send request data to the server immediately (for long running scripts).
     *
     * @param string $scriptName
     * @param int    $flag
     *
     * @return $this
     */
    public function flush($scriptName = '', $flag = self::PINBA_FLUSH_ONLY_STOPPED_TIMERS)
    {
        if ($this->isEnabled()) {
            if ($scriptName || $flag) {
                pinba_flush($scriptName, $flag);
            } else {
                pinba_flush();
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPinboardTags()
    {
        if (empty($this->pinboardTags) && $this->isEnabled()) {
            $pinbaData = pinba_get_info();

            if (isset($pinbaData['hostname'])) {
                $this->pinboardTags['__hostname'] = $pinbaData['hostname'];
            }
            if (isset($pinbaData['server_name'])) {
                $this->pinboardTags['__server_name'] = $pinbaData['server_name'];
            }
        }

        return $this->pinboardTags;
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
     * For manual disabled sending data to Pinba server
     *
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
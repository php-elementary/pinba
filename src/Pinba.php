<?php

namespace elementary\monitoring;

use elementary\core\Singleton\SingletonTrait;
use InvalidArgumentException;
use OutOfRangeException;

class Pinba
{
    use SingletonTrait;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $timers = array();


    /**
     * @param string $name Внутреннее имя, не передается на сервер Pinba. Требуется для остановки и удаления timer
     * @param array $tags
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function timerStart($name, array $tags)
    {
        if ($this->isEnabled()) {
            if ($this->isTimerExists($name)) {
                throw new InvalidArgumentException('Такой timer уже существет: '. $name);
            }

            $this->setTimer($name, pinba_timer_start($tags));
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     * @throws OutOfRangeException
     */
    public function timerStop($name)
    {
        if ($this->isEnabled()) {
            pinba_timer_stop($this->getTimer($name));

            $this->unsetTimer($name);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     * @throws OutOfRangeException
     */
    public function timerDelete($name)
    {
        if ($this->isEnabled()) {
            pinba_timer_delete($this->getTimer($name));
        }

        $this->unsetTimer($name);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return array
     * @throws OutOfRangeException
     */
    public function timerGetInfo($name)
    {
        if ($this->isEnabled()) {
            return pinba_timer_get_info($this->getTimer($name));
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
     * @return array
     */
    protected function getTimers()
    {
        return $this->timers;
    }

    /**
     * @param string $name
     *
     * @return resource
     * @throws OutOfRangeException
     */
    protected function getTimer($name)
    {
        if ($this->isTimerExists($name)) {
            throw new OutOfRangeException('Timer не найден: ' . $name);
        }

        return $this->timers[$name];
    }

    /**
     * @param string   $name
     * @param resource $value
     *
     * @return $this
     */
    protected function setTimer($name, $value)
    {
        $this->timers[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    protected function unsetTimer($name)
    {
        unset($this->timers[$name]);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isTimerExists($name)
    {
        return array_key_exists($name, $this->getTimers());
    }

//    public function timerStart($name, array $tags, array $data = array())
//    {
//        if (array_key_exists($name, $this->timers))
//            throw new WrongArgumentException('a timer with the same name allready exists');
//
//        if ($this->isTreeLogEnabled()) {
//
//            $id = uniqid();
//            $tags['treeId'] = $id;
//
//            if (!empty($this->queue))
//                $tags['treeParentId'] = end($this->queue);
//            else
//                $tags['treeParentId'] = 'root';
//
//            $this->queue[] = $id;
//        }
//
//        $this->timers[$name] =
//            count($data)
//                ? pinba_timer_start($tags, $data)
//                : pinba_timer_start($tags);
//
//        return $this;
//    }

//    public function timerStop($name)
//    {
//        if ($this->isTreeLogEnabled())
//            array_pop($this->queue);
//
//        if (!array_key_exists($name, $this->timers))
//            throw new WrongArgumentException('have no any timer with name '.$name);
//
//        pinba_timer_stop($this->timers[$name]);
//
//        unset($this->timers[$name]);
//
//        return $this;
//    }


//    public function timerDelete($name)
//    {
//        if (!array_key_exists($name, $this->timers))
//            throw new WrongArgumentException('have no any timer with name '.$name);
//
//        pinba_timer_delete($this->timers[$name]);
//
//        unset($this->timers[$name]);
//
//        return $this;
//    }
//
//    public function timerGetInfo($name)
//    {
//        if (!array_key_exists($name, $this->timers))
//            throw new WrongArgumentException('have no any timer with name '.$name);
//
//        return pinba_timer_get_info($this->timers[$name]);
//    }

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
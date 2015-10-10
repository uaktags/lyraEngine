<?php
namespace Lyra;

use ArrayAccess;

class Config implements ArrayAccess
{
    /**
     * @var array $config
     */
    protected $config;

    /**
     * Config Constructor
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->config) ? $this->config[$offset] : null;
    }

    /**
     * Config::get
     * Gets a config setting
     * @param $key
     * @return array
     */
    public function get($key)
    {
        $array = $this->config;
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if(!array_key_exists($segment, $array))
                return null;
            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Config::set
     * Sets a config setting
     * @param $key
     * @param null $value
     * @return int|null
     */
    public function set($key, $value = null)
    {
        if (is_null($key)) {
            return $array = $value;
        }
        if (is_array($key)) {
                $this->config = array_merge_recursive($this->config,$key);
        } else {
            $this->rSet($this->config, $key, $value);
        }

        return 0;
    }

    /**
     * Config::rSet
     * Recursively go through a Dot-delimited setting
     * @param $arr
     * @param $path
     * @param $value
     */
    public function rSet(&$arr, $path, $value)
    {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function destroyAll()
    {
        //die(var_dump(debug_backtrace()));
        return $this->config = array();
    }
}
<?php
namespace Lyra\Proxy;

abstract class Proxy
{

    protected static $container;

    public static function setContainer($container)
    {
        static::$container = $container;
    }

    protected static function getProxyAccessor()
    {
        throw new \RuntimeException('Proxy does not implement getProxyAccessor method.');
    }


    public static function __callStatic($method, $args)
    {
        $accessor = static::getProxyAccessor();

        $instance = static::$container[$accessor];

        switch (count($args)) {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }
}

<?php

namespace Lyra;

use Lyra\Exception;
/**
 * Common class
 * @abstract
 */
abstract class Common implements \Lyra\Interfaces\Common
{
    /**
     * Common setters and getters
     * @param string $name
     * @param mixed $arguments
     * @returns mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $action = substr($name, 0, 3);

        if ($action == 'get' || $action == 'set') {
            $property = lcfirst(substr($name, 3));

            if (property_exists($this, $property)) {
                $reflection = new \ReflectionObject($this);

                if ($reflection->getProperty($property)->isPublic()) {
                    if ($action == 'get') {
                        return $this->{$property};
                    } else {
                        $this->{$property} = $arguments ? $arguments[0] : null;

                        return $this;
                    }
                }
            }
        }

        throw new Exception('Not implemented: ' . get_called_class() . '::' . $name);
    }
}

<?php
namespace Lyra\Proxy;

class Cache extends Proxy
{

    protected static function getProxyAccessor()
    {
        return 'cache';
    }

}

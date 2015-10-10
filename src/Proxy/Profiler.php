<?php
namespace Lyra\Proxy;

class Profiler extends Proxy
{

    protected static function getProxyAccessor()
    {
        return 'profiler';
    }

}

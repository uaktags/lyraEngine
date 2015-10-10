<?php
namespace Lyra\Proxy;

class App extends Proxy
{

    protected static function getProxyAccessor()
    {
        return 'app';
    }

}

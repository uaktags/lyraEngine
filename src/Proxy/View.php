<?php
namespace Lyra\Proxy;

class View extends Proxy
{

    protected static function getProxyAccessor()
    {
        return 'view';
    }

}

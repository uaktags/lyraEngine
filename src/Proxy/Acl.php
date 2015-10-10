<?php
namespace Lyra\Proxy;

class Acl extends Proxy
{

    protected static function getProxyAccessor()
    {
        return 'acl';
    }

}

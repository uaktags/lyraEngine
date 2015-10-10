<?php

namespace Lyra;

/**
 * Library class
 * @abstract
 */
abstract class Library extends Common implements \Lyra\Interfaces\Library
{
    /**
     * Application instance
     * @var Interfaces\App
     */
    protected $app;

    /**
     * Set application instance
     * @param \Lyra\Interfaces\App $app
     * @return \Lyra\Interfaces\Library
     */
    public function setApp(\Lyra\Interfaces\App $app)
    {
        $this->app = $app;

        return $this;
    }
}

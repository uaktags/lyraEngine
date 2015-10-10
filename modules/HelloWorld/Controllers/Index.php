<?php

namespace Module\HelloWorld\Controllers;

use Module\HelloWorld\Models\Example as ExampleModel,
    App;

/**
 * Index controller
 */
class Index extends \Lyra\Controller
{
    /**
     * Page title
     * @var string
     */
    protected $title = 'Hello, world!';

    /**
     * Default action
     * @param $args array
     */
    public function index(array $args = array())
    {
        \App::setVariable('pageTitle', 'Hello, World.');
        \App::setVariable('helloWorld', 'This does work.<br>');
        \App::setConfig('hello', 'Hello World');
        echo \App::getConfig('hello');
    }
}

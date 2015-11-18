<?php
namespace Module\Error404\Controllers;
/**
 * Index controller
 */
class Index extends \Lyra\Controller
{
    /**
     * Page title
     * @var string
     */
    protected $title = 'Error 404';
    /**
     * Default action
     */
    public function index()
    {
        if (!headers_sent()) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
        }
		\View::setTemplate('Error.twig');
    }
}

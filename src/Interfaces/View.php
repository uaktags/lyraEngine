<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 5/29/2015
 * Time: 8:40 PM
 */

namespace Lyra\Interfaces;
use Twig_Extension_Debug;
use Twig_Extension_Optimizer;
use Twig_Loader_Filesystem;
use Twig_NodeVisitor_Optimizer;

interface View extends Common{

    public function __construct(\Lyra\Interfaces\Container $container, $loader);

    /**
     * Register helpers
     */
    protected function registerHelpers();
    /**
     * set the template-path or use the path from config
     *
     * @param array|string $templatePath
     *
     * @return bool
     */
    private function setTemplatePath($templatePath);
    /**
     * check if the the template-directory exists
     *
     * @param string $templatePath
     * @param bool   $exitOnError
     *
     * @return bool
     */
    private function checkTemplatePath($templatePath, $exitOnError = true);
    /**
     * set the environment-config
     *
     * @param $environment
     */
    private function setEnvironment($environment);
    /**
     * optimize twig-output
     *
     * OPTIMIZE_ALL (-1) | OPTIMIZE_NONE (0) | OPTIMIZE_FOR (2) | OPTIMIZE_RAW_FILTER (4) | OPTIMIZE_VAR_ACCESS (8)
     *
     */
    private function optimizer();
    /**
     * clear TwigWrapper-Cache && exit()
     */
    public function clearTwigCache();
    /**
     * loads default-data into TwigWrapper
     */
    public function loadData();

    /**
     * render the template
     *
     * @param bool $withHeader
     *
     * @return string
     */
    public function render( $withHeader = true);
    /**
     * debug
     */
    public function debug();
    /**
     * show all variables
     */
    public function debug_data();

    public function get($variable);

    public function set($variable, $value = null);

    public function setTemplate($template);
    /**
     * Proxy call requests
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args) ;
}
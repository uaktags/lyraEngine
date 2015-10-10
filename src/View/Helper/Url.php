<?php

namespace Lyra\View\Helper;
use \Lyra\Interfaces\Container;

/**
 * URL
 */
class Url implements ViewHelperInterface
{
	
	protected $siteConfig;
	
	/**
	 * Expose helpers
	 */
	public $helpers = array(
		'getUrl',
		'getThemeUrl',
	);		
	
	public function __construct(Container $container)
	{
		$this->siteConfig =  $container['config']['site'];
	}
	
	/**
	 * Format a complete URL
	 * @param string $uri
	 * @return string
	 */
	public function getUrl($uri)
	{
		return $this->siteConfig['url'] . '/' . $uri;
	}
	
	/**
	 * Format a complete URL for theme
	 * @param string $uri
	 * @return string
	 */
	public function getThemeUrl($uri)
	{
		return $this->siteConfig['url'] . '/theme/' . $this->siteConfig['theme'] . '/' . $uri;
	}
}
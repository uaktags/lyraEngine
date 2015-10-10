<?php
namespace Lyra;

/**
 * Cache
 */
class Cache implements \Lyra\Interfaces\Cache
{
	protected $prefix;
	protected $ttl;
    public $container;
	
	/**
	 * Construct
	 * @param Container $container
	 * @throws \Exception
	 */
	public function __construct(\Lyra\Interfaces\Container &$container)
	{
		$this->container = $container;

		// assume no caching should be used if not present
		// within configuration
        $cache = \Config::get('cache');
		if (!isset($cache)) {
			return;
		}

		$cache_config = \Config::get('cache');
		if ($cache_config['use'] == false) {
			return;
		}

		$this->prefix = $cache_config['prefix'];
		$this->ttl = $cache_config['ttl'];
		
		if (!function_exists('apc_fetch')) {
			throw new \Exception('Current configuration does not support APC');
		}

		// expose the Cache object
		$container['cache'] = $this;
	}
	
	/**
	 * setTtl
	 * @param int $ttl
	 */
	public function setTtl($ttl) {
		$this->ttl = $ttl;
	}
	
	/**
	 * offsetExists
	 * @param string $offset
	 */
	public function offsetExists($offset)
	{
		return apc_exists($this->prefix . $offset);
	}

	/**
	 * offsetGet
	 * @param string $offset
	 */	
	public function offsetGet($offset)
	{
		return apc_fetch($this->prefix . $offset);
	}

	/**
	 * offsetSet
	 * @param string $offset
	 * @param string $value
	 */	
	public function offsetSet($offset, $value)
	{
		apc_store($this->prefix . $offset, $value, $this->ttl);
	}

	/**
	 * offsetUnset
	 * @param string $offset
	 */	
	public function offsetUnset($offset)
	{
		apc_delete($this->prefix . $offset);
	}
}
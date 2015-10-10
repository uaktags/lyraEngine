<?php

namespace Lyra\Model;

use Lyra\Model;
use \Exception;
use	\InvalidArgumentException;

/**
 * Player
 * @see Library\Model
 */
class Module extends Model
{
	/**
	 * Random state
	 * @var string
	 */
	private $randomState;
	
	protected $useCaching = false;

    protected $tableName = 'modules';
	
	public function getAll()
	{
		return parent::findAll();
	}
	
	public function isActive($data)
	{
		return (bool) parent::find($data,'title', 'active');
	}

    public function create($data)
    {
        return parent::add($data);
    }
}
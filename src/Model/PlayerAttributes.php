<?php

namespace Lyra\Model;

use Lyra\Model;
use \Exception;
use	\InvalidArgumentException;

/**
 * Player
 * @see Library\Model
 */
class PlayerAttributes extends Model
{
	
	protected $useCaching = false;

    protected $tableName = 'player_attributes';
	
	/**
	 * create
	 * Exception codes
	 * 1 - Invalid email
	 * 2 - Invalid username
	 * 4 - Username in use
	 * 8 - Email in use
	 * 16 - Invalid confirm password
	 * 32 - Invalid password
	 * 
	 * @param array $data
	 * @throws Exception
	 * @throws InvalidArgumentException
	 * @return unknown
	 */
	public function create($data)
	{
		return $data;
	}

    public function getAll()
    {
        $attrs = $this->query('select att.title, patt.value FROM <prefix>player_attributes patt INNER JOIN <prefix>attributes att ON att.attribute_id = patt.attribute_id WHERE patt.player_id = 1');
        $attrs = $attrs->fetchAll();
        return $attrs;

    }
}
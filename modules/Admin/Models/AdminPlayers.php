<?php

namespace Module\Admin\Models;

use Lyra\Model;

/**
 * Installer
 * @see \Lyra\Model
 */
class AdminPlayers extends Model
{
    protected $useCaching = false;

    protected $tableName = 'players';

    /**
     * getTotal
     * @return object
     */
    public function getTotal()
    {
        $query = $this->query('SELECT count(*) as "num" FROM <prefix>players');
        return $query->fetch(\Pdo::FETCH_ASSOC)['num'];
    }
}
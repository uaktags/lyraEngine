<?php

namespace Lyra\Model;

use Lyra\Model,
    Lyra\Interfaces\App;

/**
 * PDO
 * @see Abstracts\Model
 */
class Pdo extends Model
{
    /**
     * Handle
     * @var handle
     */
    protected $handle;

    public $tableName;
    /**
     * Constructor
     * Establish database connection
     * @param \Lyra\Interfaces\App|object $app
     * @throws \Exception
     */
    /*public function __construct(App $app)
    {
        parent::__construct($app);

        $config = $this->app->config['db'];

        try {
            $this->handle = new \PDO($config['driver'] . ':host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['database'] . ';charset=utf8',
                $config['username'], $config['password']);
        } catch (\PDOException $e) {
            throw new \Exception('Error establishing database connection: ' . $e->getMessage());
        }

        $this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->handle->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    }*/

    public function getHandle()

    {
        return $this->handle;
    }
}
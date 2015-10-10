<?php

namespace Lyra\Model;

use Lyra\Model,
    Lyra\Interfaces\Container;

/**
 * Session
 * @see Abstracts\Model
 */
class Session extends Model
{
    protected $messages = array(
        'INFO' => '',
        'WARN' => '',
        'FAIL' => '',
        'GOOD' => ''
    );

    public function __construct(Container $container)
    {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Set a session value
     * @param string $variable
     * @param mixed $value
     */
    public function set($variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    /**
     * Get a session value
     * @param string $variable
     * @return mixed
     */
    public function get($variable)
    {
        if (isset($_SESSION[$variable])) {
            return $_SESSION[$variable];
        }
    }

    /**
     * Clear all session values
     */
    public function clear()
    {
        session_unset();
    }

    function generateSignature()
    {
        return sha1(1);
    }

    function compareSignature($origin)
    {
        return $origin === generateSignature();
    }

    public function setMessage($message, $level = 'info')
    {
        $level = strtoupper($level);

        // for better practices.
        if (array_key_exists($level, $this->messages) === false) {
            throw new \Exception('Message level "' . $level . '" does not exists.');

            return false;
        }

        $this->messages[$level] .= $message;

        return true;
    }

    public function validPlayer()
    {
        if (!($playerId = $this->get('player_id'))) {
            header('HTTP/1.0 403 Forbidden');

            header('Location: index');

            exit;
        }

        return $playerId;
    }

    public function isLoggedIn()
    {
        if ($this->get('player_id')) {
            return true;
        }

        return false;
    }

    public function getPlayerId()
    {
        return (integer) $this->get('player_id');
    }
}

<?php namespace Opus;

/**
 * Class Session
 *
 * This class will handle all session configurations
 *
 * @package Opus
 */

class Session {

    /**
     * Indicates if the session has started
     *
     * @var bool
     */
    public static $started = false;

    /**
     * Start the session
     *
     * @return void
     */
    public static function start() {
        session_start();

        static::$started = true;
    }
}
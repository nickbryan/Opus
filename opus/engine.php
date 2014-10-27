<?php

    /**
     * Opus - Simple PHP Framework
     *
     * @package Opus
     * @Version 1.0
     * @author Nicholas Bryan <nickbryan.music@gmail.com>
     */

    // Change php working directory to app root
    chdir(ROOT);

    // Register the global AutoLoader
    require_once(ROOT . DS . "vendor" . DS ."autoload.php");

    // Register Class Imports
    use Opus\Config;
    use Opus\Application;

    // Set path aliases
    Application::setPaths(array(
        'root'      => ROOT,
        'app'       => ROOT . DS . "application",
        'tmp'       => ROOT . DS . "tmp",
        'config'    => ROOT . DS . "application" . DS . "config",
        'public'    => ROOT . DS . "public",
        'opus'      => ROOT . DS . "opus",
        'vendor'    => ROOT . DS . "vendor"
    ));

    //Register AutoLoader
    $controllers = new \Opus\AutoLoad('Controller', Application::getPath('app') . DS . 'controllers');
    $controllers->register();
    Controller\IndexController::test();

    //Register timezone
    if (Config::get('app.timezone')) {
        date_default_timezone_set(Opus\Config::get('app.timezone'));
    }

    // Set error reporting based on environment
    if (Config::get('app.environment') == 'development') {
        error_reporting(E_ALL);
        ini_set('display_erros', 'On');
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', 'Off');
        ini_set('log_errors', 'On');
        ini_set('error_log', ROOT . DS . 'tmp' . DS . 'logs' . DS . 'error.log');
    }

    // Start the application
    Application::initialise();

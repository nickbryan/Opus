<?php namespace Opus;

/**
 * Class Router
 *
*                    // Match all request URIs
[i]                  // Match an integer
[i:id]               // Match an integer as 'id'
[a:action]           // Match alphanumeric characters as 'action'
[h:key]              // Match hexadecimal characters as 'key'
[:action]            // Match anything up to the next / or end of the URI as 'action'
[create|edit:action] // Match either 'create' or 'edit' as 'action'
[*]                  // Catch all (lazy, stops at the next trailing slash)
[*:trailing]         // Catch all as 'trailing' (lazy)
[**:trailing]        // Catch all (possessive - will match the rest of the URI)
.[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional

 *
 * @package Opus
 */

class Router extends Route {
    /**
     * Contains the base path, used when application is run from
     * a sub directory.
     *
     * @var string
     */
    private $basePath = '';

    /**
     * Holds all current routes.
     *
     * @var array
     */
    private $routes = array();

    /**
     * Holds a key, value list of routes so a route can be called
     * by its declared name.
     *
     * @var array
     */
    private $namedRoutes = array();

    /**
     * TODO: understand this!
     *
     * @var array
     */
    private $matchTypes = array(
        'i'  => '[0-9]++',
        'a'  => '[0-9A-Za-z]++',
        'h'  => '[0-9A-Fa-f]++',
        '*'  => '.+?',
        '**' => '.++',
        ''   => '[^/\.]++'
    );

    /**
     * Allows creation of router in one call
     *
     * @access public
     * @param array $routes
     * @param string $basePath
     */
    public function __construct($routes = array(), $basePath = '')
    {
        $this->addRoutes($routes);
        $this->setBasePath($basePath);
    }

    /**
     * Used to set the base path. Useful for applications that
     * live inside a sub directory.
     *
     * @access public
     * @param $basePath
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Maps a route to its target.
     *
     * @access public
     * @param $method
     * @param $route
     * @param $target
     * @param null $name
     * @throws \Exception
     * @return void
     */
    public function map($method, $route, $target, $name = null)
    {
        if ($this->methodCheck($method) === false) {
            throw new \Exception("[{$method}] is not a valid method type.");
        }
        $this->routes[] = array(
            $method,
            $route,
            $target,
            $name
        );

        if ($name) {
            if (isset($this->namedRoutes[$name])) {
                throw new \Exception("Route can not be declared more than once [{$name}].");
            } else {
                $this->namedRoutes[$name] = $route;
            }
        }
    }

    /**
     * Add multiple routes at the same time.
     *
     * $routes = array(
     *     array(
     *         $method,
     *         $route,
     *         $target,
     *         $name
     *     )
     *  );
     *
     * @access public
     * @param $routes
     * @return void
     * @throws \Exception
     */
    public function addRoutes($routes)
    {
        if (is_array($routes) === false && $routes instanceof \Traversable === false) {
            throw new \Exception('Route should be an array or instance of Traversable');
        }
        foreach ($routes as $route) {
            call_user_func_array(array($this, 'map'), $route);
        }
    }

    /**
     * Generates the Url for a named route. Allows for reversed routing.
     *
     * @access public
     * @param $routeName
     * @param array $params
     * @return mixed|string
     * @throws \Exception
     */
    public function generate($routeName, array $params = array())
    {
        if (isset($this->namedRoutes[$routeName]) === false) {
            throw new \Exception("Route '{$routeName}' does not exist.");
        }

        $route = $this->namedRoutes[$routeName];

        $url = $this->basePath . $route;

        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if ($pre) {
                    $block = substr($block, 1);
                }

                if (isset($params[$param])) {
                    $url = str_replace($block, $params[$param], $url);
                } else if ($optional) {
                    $url = str_replace($pre . $block, '', $url);
                }
            }
        }

        return $url;
    }

    /**
     * Matches a given Request Url against the stored routes.
     *
     * @param null $requestUrl
     * @param null $requestMethod
     * @return array|bool
     */
    public function match($requestUrl = null, $requestMethod = null)
    {
        $params = array();
        $match = false;

        // Set request url if it isn't passed as a parameter
        if ($requestUrl === null) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $requestUrl = $_SERVER['REQUEST_URI'];
            } else {
                $requestUrl = '/';
            }
        }

        // Strip base path from url
        $requestUrl = substr($requestUrl, strlen($this->basePath));

        // Strip query string from url
        $strpos = strpos($requestUrl, '?');
        if ($strpos !== false) {
            $requestUrl = substr($requestUrl, 0, $strpos);
        }

        // Set request method if it isn't passed as a parameter
        if ($requestMethod === null) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                $requestMethod = $_SERVER['REQUEST_METHOD'];
            } else {
                $requestMethod = 'GET';
            }
        }

        // Force request_order to be get then post
        $_REQUEST = array_merge($_GET, $_POST);

        foreach($this->routes as $handler) {
            list($method, $_route, $target, $name) = $handler;

            $methods = explode('|', $method);
            $method_match = false;

            // Check if request matches
            foreach ($methods as $method) {
                if (strcasecmp($requestMethod, $method) === 0) {
                    $method_match = true;
                    break;
                }
            }

            // If request doesn't match, continue to next route
            if ($method_match === false) {
                continue;
            }

            if ($_route === '*') {
                $match = true;
            } else if (isset($_route[0]) && $_route[0] === '@') {
                $pattern = '`' . substr($_route, 1) . '`u';
            } else {
                $route = null;
                $regex = false;
                $j = 0;
                $n = isset($_route[0]) ? $_route[0] : null;
                $i = 0;

                // Find the longest non-regex substring and match it against the URI
                while (true) {
                    if (isset($_route[$i]) === false) {
                        break;
                    } else if ($regex === false) {
                        $c = $n;
                        $regex = $c === '[' || $c === '(' || $c === '.';
                        if ($regex === false && isset($_route[$i + 1]) !== false) {
                            $n = $_route[$i + 1];
                            $regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
                        }
                        if ($regex === false && $c !== '/' && (!isset($requestUrl[$j]) || $c !== $requestUrl[$j])) {
                            continue 2;
                        }
                        $j++;
                    }
                    $route .= $_route[$i++];
                }

                $regex = $this->compileRoute($route);
                $match = preg_match($regex, $requestUrl, $params);
            }

            if (($match == true || $match > 0)) {
                if ($params) {
                    foreach ($params as $key => $value) {
                        if (is_numeric($key)) {
                            unset($params[$key]);
                        }
                    }
                }
                return array(
                    'target' => $target,
                    'params' => $params,
                    'name'   => $name
                );
            }
        }
        return false;
    }

    /**
     * Compiles the regex for a given route.
     *
     * @access public
     * @param $route
     * @return string
     */
    private function compileRoute($route)
    {
        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
            $matchTypes = $this->matchTypes;
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($matchTypes[$type])) {
                    $type = $matchTypes[$type];
                }
                if ($pre === '.') {
                    $pre = '\.';
                }

                // Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                    . ($pre !== '' ? $pre : null)
                    . '('
                    . ($param !== '' ? "?P<$param>" : null)
                    . $type
                    . '))'
                    . ($optional !== '' ? '?' : null);

                $route = str_replace($block, $pattern, $route);
            }
        }
        return "`^$route$`u";
    }
}
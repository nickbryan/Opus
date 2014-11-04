<?php

$router->map('GET', '/', function(){
    \Opus\Response::make('This is the home page');
});

$router->map('GET', '/about/', function(){
    \Opus\Response::make('This is the about page');
});
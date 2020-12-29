<?php

require_once './vendor/autoload.php';


class Router extends Roller\Router {
    function delete($path, $callback, $options=array()) {
        $options['method'] = 'delete';
        return $this->add($path,  $callback , $options);
    }

    function put($path, $callback, $options=array()) {
        $options['method'] = 'put';
        return $this->add($path,  $callback , $options);
    }
}

class RouteSet extends Roller\RouteSet {
    function delete($path, $callback, $options=array()) {
        $options['method'] = 'delete';
        return $this->add($path,  $callback , $options);
    }

    function put($path, $callback, $options=array()) {
        $options['method'] = 'put';
        return $this->add($path,  $callback , $options);
    }
}
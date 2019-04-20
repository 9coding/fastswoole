<?php

namespace FastSwoole;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Fastapi\Service\ConfigProvider;
use Fastapi\Service\ErrorProvider;
use Fastapi\Service\LogProvider;
use Fastapi\Service\LanguageProvider;
use Fastapi\Service\RouteProvider;
use Fastapi\Pool\Mysql;
use Fastapi\Exception\ServerException;
use Fastapi\Middleware\BaseMiddleware;
use League\Pipeline\StageInterface;

class Core {
    
    public static $container;

    public static function init($mode) {
        self::$container = new Container();
        
        self::registeService();
        BaseMiddleware::registeMiddleware();
        $dbConfig = self::$container['config'];
        Mysql::init($dbConfig['mysql']);
        return new static;
    }
    
    private static function registeService() {
        self::$container->register(new ConfigProvider());
        self::$container->register(new LogProvider());
        self::$container->register(new ErrorProvider());
    }
    
    public function addService($service = '') {
        if ($service instanceof ServiceProviderInterface) {
            self::$container->register($service);
            return true;
        } else {
            return false;
        }
    }
    
    public function addMiddleware($middleClass = '', $params = '') {
        if ($middleClass instanceof StageInterface) {
            $middleware = get_class($middleClass);
            self::$container['middleware_param'] = $params;
            self::$container[$middleware] = function ($c) use ($middleClass) {
                return new $middleClass($c['middleware_param']);
            };
            return true;
        } else {
            return false;
        }
    }
}

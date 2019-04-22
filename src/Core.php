<?php

namespace FastSwoole;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Service\ConfigProvider;
use FastSwoole\Service\ErrorProvider;
use FastSwoole\Service\LogProvider;
use FastSwoole\Service\MiddlewareProvider;
use League\Pipeline\StageInterface;
use FastSwoole\Pool\Mysql as MysqlPool;

class Core {
    
    public static $app;

    public static function init() {
        self::$app = new Container();
        self::registeService();
        $modeService = self::$app['config']->get('app.'.MODE.'.service', []);
        foreach ($modeService as $service) {
            if (class_exists($service)) {
                self::addService(new $service);
            }
        }
        $mysqlpool = new MysqlPool();
        $mysqlpool->setDefer();
        self::$app['mysql'] = $mysqlpool;
    }
    
    private static function registeService() {
        self::$app->register(new ConfigProvider());
        self::$app->register(new LogProvider());
        self::$app->register(new ErrorProvider());
        self::$app->register(new MiddlewareProvider());
    }
    
    public static function addService($service = '') {
        if ($service instanceof ServiceProviderInterface) {
            self::$app->register($service);
            return true;
        } else {
            return false;
        }
    }
    
    public static function addMiddleware($middleClass = '', $params = '') {
        if ($middleClass instanceof StageInterface) {
            $middleware = get_class($middleClass);
            self::$app[$middleware] = function ($c) use ($middleClass, $params) {
                return new $middleClass($params);
            };
            return true;
        } else {
            return false;
        }
    }
}

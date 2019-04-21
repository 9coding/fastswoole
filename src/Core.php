<?php

namespace FastSwoole;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Fastapi\Service\ConfigProvider;
use Fastapi\Service\ErrorProvider;
use Fastapi\Service\LogProvider;
use Fastapi\Service\MiddlewareProvider;
use League\Pipeline\StageInterface;

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

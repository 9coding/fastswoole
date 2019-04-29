<?php

namespace FastSwoole\Functions;

trait ClassMethod {
    
    public function analyzeMethod($className, $methodName) {
        if (class_exists($className)) {
            $reflaction = new \ReflectionClass($className);
            if ($reflaction->hasMethod($methodName)) {
                return true;
            }
        }
        return false;
    }

    public function analyzeParameter($className, $methodName) {
        $reflactionMethod = new \ReflectionMethod($className, $methodName);
        $methodParams = array();
        foreach ($reflactionMethod->getParameters() as $param) {
            $paramType = $param->getType();
            if (!$paramType->isBuiltin()) {
                $paramObject = strval($paramType);
                $methodParams[] = new $paramObject;
            } else {
                $methodParams[] = $param->getDefaultValue();
            }
        }
        return $methodParams;
    }
    
    public function dispatchMethod($server, $className, $methodName, ...$data) {
        $result = false;
        $allowDispatch = $this->analyzeMethod($className, $methodName);
        if ($allowDispatch) {
            $methodParams = $this->analyzeParameter($className, $methodName);
            $controller = new $className($server, $data);
            $result = call_user_func_array(array($controller, $methodName), $methodParams);
        }
        return $result;
    }
}

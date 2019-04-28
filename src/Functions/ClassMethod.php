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
}

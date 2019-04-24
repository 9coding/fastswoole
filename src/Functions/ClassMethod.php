<?php

namespace FastSwoole\Functions;

trait ClassMethod {

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

<?php
namespace FastSwoole;

class Config {

    private $config = [];
    
    public function load() {
        foreach (glob(CONFIG_DIR.'/*.php') as $configFile) {
            $filename = explode('.', basename($configFile));
            $this->config[$filename[0]] = include_once $configFile;
        }
    }
    
    public function get($key, $default = '') {
        if (isset($this->config['fastswoole_custom'][$key])) {
            return $this->config['fastswoole_custom'][$key];
        }
        $keys = explode('.', $key);
        $configValue = $this->config;
        foreach ($keys as $value) {
            if (isset($configValue[$value])) {
                $configValue = $configValue[$value];
            } else {
                $configValue = $default;
                break;
            }
        }
        return $configValue;
    }
    
    public function set($key, $value) {
        $this->config['fastswoole_custom'][$key] = $value;
    }
}

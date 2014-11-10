<?php

namespace Oneup\AclBundle\Driver;

class DriverChain
{
    protected $drivers;

    public function addDriver(DriverInterface $driver)
    {
        $this->drivers[] = $driver;
    }

    public function getDriver()
    {
        return $this->drivers;
    }

    public function readMetaData(\ReflectionClass $class)
    {
        $data = array();

        foreach ($this->drivers as $driver) {
            $metaData = $driver->readMetaData($class);

            if (is_array($metaData)) {
                $data = $this->merge($data, $metaData);
            }
        }

        return $data;
    }

    private function merge(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}

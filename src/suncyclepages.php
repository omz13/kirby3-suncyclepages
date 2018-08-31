<?php

// phpcs:disable Squiz.Commenting.ClassComment.Missing
// phpcs:disable Squiz.Commenting.VariableComment.Missing
// phpcs:disable Squiz.Commenting.FunctionComment.Missing

namespace omz13;

define('SUNCYCLE_VERSION', '0.0.0');

class SunCyclePages
{

    public static $version = SUNCYCLE_VERSION;


    public static function ping(): string
    {
        return static::class.' pong '.static::$version;

    }//end ping()


    public static function getConfigurationForKey(string $key, $default=null)
    {
        $o = option('omz13.suncyclepages.' . $key);

        if (isset($o) == true) {
            return $o;
        }

        return $default;

    }


    public static function isEnabled(): bool
    {
        return static::getConfigurationForKey('disable', false) != true;

    }


}//end class

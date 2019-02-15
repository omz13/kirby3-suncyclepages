<?php

// phpcs:disable Squiz.Commenting.ClassComment.Missing
// phpcs:disable Squiz.Commenting.VariableComment.Missing
// phpcs:disable Squiz.Commenting.FunctionComment.Missing

namespace omz13;

use const SUNCYCLE_CONFIGURATION_PREFIX;
use const SUNCYCLE_VERSION;

use function array_key_exists;
use function define;
use function is_array;
use function kirby;

define( 'SUNCYCLE_VERSION', '1.1.0' );
define( 'SUNCYCLE_CONFIGURATION_PREFIX', 'omz13.suncyclepages' );


class SunCyclePages
{

  public static function version() : string {
    return SUNCYCLE_VERSION;
  }//end version()

  public static function ping() : string {
      return static::class . ' pong ' . static::version();
  }//end ping()

  public static function getConfigurationForKey( string $key, ?string $default = null ) : ?string {
    $o = kirby()->option( SUNCYCLE_CONFIGURATION_PREFIX . '.' . $key );

    if ( isset( $o ) == true ) {
      return $o;
    }

    return $default;
  }//end getConfigurationForKey()

  public static function getArrayConfigurationForKey( string $key ) : array {
    // Try to pick up configuration when provided in an array (vendor.plugin.array(key=>value))
    $o = kirby()->option( SUNCYCLE_CONFIGURATION_PREFIX );
    if ( $o != null && is_array( $o ) && array_key_exists( $key, $o ) ) {
      return $o[$key];
    }

    // try to pick up configuration as a discrete (vendor.plugin.key=>value)
    $o = kirby()->option( SUNCYCLE_CONFIGURATION_PREFIX . '.' . $key );
    if ( $o != null ) {
      return $o;
    }

    // this should not be reached... because plugin should define defaults for all its options...
    return [];
  }//end getArrayConfigurationForKey()

  public static function isEnabled() : bool {
    if ( static::getConfigurationForKey( 'disable' ) == true ) {
      return false;
    } else {
      return true;
    }
  }//end isEnabled()
}//end class

<?php

// phpcs:disable PHPCompatibility.PHP.NewClosure.ThisFoundOutsideClass

Kirby::plugin(
    'omz13/suncyclepages',
    [
      'root'         => dirname( __FILE__, 2 ),
      'options'      => ['disable' => false],
      'hooks'        => [
        // phpcs:ignore
        'route:after' => function ( $route, string $path, string $method, $result ) {

          if ( $route->env() != 'site' ) {
              return;
          }

          assert( $result instanceof Kirby\Cms\Page );

          if ( omz13\SunCyclePages::isEnabled() == false ) {
              return false;
          }

          if ( property_exists( $result, 'content' ) == false ) {
              return;
          }

          // belt-and-braces guarding
          if ( $result->hasMethod( 'issunset' ) == true ) {
            if ( $result->callMethod( 'issunset' ) == true ) {
              if ( $result->content()->has( 'sunsetto' ) ) {
                $to = $result->content()->get( 'sunsetto' );
                if ( kirby()->option( 'debug' ) == 'true' ) {
                  header( 'X-SUNCYCLE: isSunset to ' );
                }
                if ( $to != "" ) {
                  go( $to, 301 );
                }
              }

              // because
              header( 'X-SUNCYCLE: isSunset' );
              // 410 = Gone.
              echo Kirby\Cms\Response::errorPage( [], 'html', 410 );
              die;
            }//end if
          }//end if

          if ( $result->hasMethod( 'isunderembargo' ) == true ) {
            if ( $result->callMethod( 'isunderembargo' ) == true ) {
              if ( kirby()->option( 'debug' ) == 'true' ) {
                header( 'X-SUNCYCLE: isUnderembargo' );
              }

              echo Kirby\Cms\Response::errorPage( [], 'html', 404 );
              die;
            }
          }
        },
      ],
      'pageMethods'  => [
        'issunset'       => function () {
          if ( omz13\SunCyclePages::isEnabled() == false ) {
              return false;
          }

          $timestamp = strtotime( $this->content()->get( 'sunset' ) );
          if ( $timestamp != 0 && $timestamp < time() ) {
              return true;
          }

          return false;
        },
        'isunderembargo' => function () {
          if ( omz13\SunCyclePages::isEnabled() == false ) {
              return false;
          }

          if ( $this->content()->get( 'embargo' ) == 'true' ) {
            $timestamp = strtotime( $this->content()->get( 'date' ) );
            if ( $timestamp != 0 && time() < $timestamp ) {
                return true;
            }
          }

            return false;
        },
      ],
      'pagesMethods' => [
        'isunderembargo' => function ( $match = true ) {
          if ($match) { // phpcs:ignore
                return $this->filterBy( 'isunderembargo', true );
          }

            return $this->filterBy( 'isunderembargo', '!=', true );
        },
        'issunset'       => function ( $match = true ) {
          if ($match) {  // phpcs:ignore
                return $this->filterBy( 'issunset', true );
          }

            return $this->filterBy( 'issunset', '!=', true );
        },
      ],
      'collections'  => [
        'isunderembargo' => function ( $site ) {
            return $site->index()->isunderembargo();
        },
        'issunsetted'    => function ( $site ) {
            return $site->index()->issunset();
        },
      ],
    ]
);

require_once __DIR__ . '/suncyclepages.php';

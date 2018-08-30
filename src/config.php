<?php

Kirby::plugin(
'omz13/suncyclepages',
  [
    'hooks'        => [
        'route:after' => function ($route, $path, $method, $result) {
            if (!isset($result) || !property_exists($result, 'content')) {
                return;
            }

            if ($result->hasMethod('issunset') == true) {
                if ($result->issunset() == true) {
                  // 410 = Gone.
                  echo Kirby\Cms\Response::errorPage([],'html',410);
                  die;
                }
            }

            if ($result->hasMethod('isunderembargo') == true) {
                if ($result->isunderembargo() == true) {
                  // TODO: remove 418 = teapot
                  echo Kirby\Cms\Response::errorPage([],'html',418);
                  die;
                }
            }

        },
    ],
    'pageMethods'  => [
        'issunset'       => function () {
            $timestamp = strtotime($this->content()->sunset());
            if ($timestamp != 0 && $timestamp < time()) {
                return true;
            }

            return false;
        },
        'isunderembargo' => function () {
            $timestamp = strtotime($this->content()->embargo());
            if ($timestamp != 0 && time() < $timestamp) {
                return true;
            }

            return false;
        },
    ],
    'pagesMethods' => [
        'isunderembargo' => function ($match=true) {
            if ($match) { // phpcs:ignore
                return $this->filterBy('isunderembargo', true);
            }

            return $this->filterBy('isunderembargo', '!=', true);
        },

        'issunset'       => function ($match=true) {
            if ($match) {  // phpcs:ignore
                        return $this->filterBy('issunset', true);
            }

            return $this->filterBy('issunset', '!=', true);
        },
    ],
    'collections'  => [
        'isunderembargo' => function ($site) {
            return $site->index()->isunderembargo();
        },
        'issunsetted'    => function ($site) {
                        return $site->index()->issunset();
        },
      ],
    ]
);

require_once __DIR__.'/suncyclepages.php';

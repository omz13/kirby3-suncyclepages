<?php

Kirby::plugin(
'omz13/suncyclepages',
  [
    'options' => [
      'disable' => false,
    ],
    'hooks'        => [
        'route:after' => function ($route, $path, $method, $result) {

            if (omz13\suncyclepages::isEnabled()==false)
              return false;

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
                  // TODO: document: in debug mode, return 418... otherwise standard 404.
                  $rc = (kirby()->option('debug') !== null && kirby()->option('debug') == true) ? 418 : 404;
                  echo Kirby\Cms\Response::errorPage([],'html',$rc);
                  die;
                }
            }

        },
    ],
    'pageMethods'  => [
        'issunset'       => function () {
            if (omz13\suncyclepages::isEnabled()==false)
              return false;
            $timestamp = strtotime($this->content()->sunset());
            if ($timestamp != 0 && $timestamp < time()) {
                return true;
            }

            return false;
        },
        'isunderembargo' => function () {
            if (omz13\suncyclepages::isEnabled()==false)
              return false;
            if ($this->content()->embargo() == "true")
            {
              $timestamp = strtotime($this->content()->date());
              if ($timestamp != 0 && time() < $timestamp) {
                  return true;
              }
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

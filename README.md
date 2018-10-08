# Kirby3 SunCyclePages

**Requirement:** Kirby 3

## Documentation

### Purpose

For a kirby3 site, this plugin [omz13/suncyclepages](https://github.com/omz13/kirby3-suncyclepages) allows the _lifecycle_ of page to be controlled vis-à-vis pages becoming generally available at a sunrise date and withdrawn at a sunset date. This enhances the limited lifecycle options provied by kirby3's `state` (unpublished, draft, and published) to better match that needed certain editorial or regulatory needs, viz: unpublished, draft, embargoed (waiting "sunrise"), published ("sunny"), withdrawn ("sunset").

- Only pages that have a status of "published" are affected, i.e. those with "draft" or "unpublished" behave as usual.
- Pages can be embargoed until being made generally available a specified date ("sunrise"). Any attempt to view the page before that date will yield the standard error page and a `404` [Not Found] code.
- Pages can be withdrawn at a specific date ("sunset"). Any attempt to view the page after that date will yield a `410` [Gone] and the standard error page, or, a `301` [Moved Permanently] to a specified location on a per-page basis.
- A debug mode can be enabled to include a header in the response to indicate that a page was under embargo or has sunset, c.f. `X-SUNCYCLE`.

#### Caveat

Kirby3 is under beta, therefore this plugin, and indeed kirby3 itself, may or may not play nicely with each other, or indeed work at all: use it for testing purposes only; if you use it in production then you should be aware of the risks and know what you are doing.

#### Roadmap

For 1.0, the non-binding list of planned features and implementation notes are:

- [x] MVP
- [x] Basic debugging
- [ ] If the time is not specified (just the date), default sensibly.
- [ ] Better debugging
- [ ] Sections for blueprints

### Installation

#### via composer

If your kirby3-based site is managed using-composer, simply invoke `composer require omz13/kirby3-suncyclepages`, or add `omz13/kirby3-suncyclepages` to the "require" component of your site's `composer.json` as necessary, e.g. to be on the bleeding-edge:

```yaml
"require": {
  ...
  "omz13/kirby3-suncyclepages": "dev-master as 1.0.0",
  ...
}
```
#### via git

Clone github.com/omz13/kirby3-suncyclepages into your `site/plugins` and then in `site/plugins/kirby3-suncyclepages` invoke ``composer update --no-dev`` to generate the `vendor` folder and the magic within.

```sh
$ git clone github.com/omz13/kirby3-suncyclepages site/plugins/kirby3-suncyclepages
$ cd site/plugins/kirby3-suncyclepages
$ composer update --no-dev
```

If your project itself is under git, then you need to add the plugin as a submodule and possibly automate the composer update; it is assumed if you are doing this that you know what to do.

### Configuration

The following mechanisms can be used to modify the plugin's behavior.

#### via `config.php`

- `omz13.suncyclepages.disable` - optional - default `false` - a boolean which, if `true`, disables the plugin.

- `omz13.suncyclepages.embargoCheckWhenTemplateIs` - optional - default `[ 'article' ]` : if a page uses a template name that is specified in this option, the embargo check (sunrise) is explicitly performed.

- `omz13.suncyclepages.embargoCheckWhenParentIs` - optional - default `[ 'blog' ]` : if a page has a parent that is specified in this parameter, the embargo check (sunrise) is explicitly performed.

### Use

#### Content fields

The plugin uses the following content fields. These are all optional; if missing or empty, they are assumed to be not applicable vis-à-via their indicated functionality.

- `skipembargo` - boolean - optional (default `false`) - if `true`, the embargo check (sunrise) is skipped (not performed) for this page.

- `embargo` - boolean -  optional (default `false`) - gf `true`, the embargo check (sunrise) should be explicitly performed against this page. This is intended to be used on pages where such checking is not normally done, i.e. not explicitly done due to parent (c.f. `omz13.suncyclepages.embargoCheckWhenParentIs`) or template (c.f. `omz13.suncyclepages.embargoCheckWhenTemplateIs`).

- `sunset` - date - optional - the (future) date after which the page is to be sunset (withdrawn).

- `sunsetto` page - optional - the name of a page to be used for `301` redirects when the page is sunset; if not specified a `404` is given.

#### Debug mode

If the kirby site is in debug mode:

- If a page is embargoed (waiting for sunrise), the `404` page will include an additional header `X-SUNCYCLE: isUnderembargo`.

- If a page has been withdrawn (sunset), the `410` response will include an additional header `X-SUNCYCLE: isSunset`, and the `301` a `X-SUNCYCLE: isSunset <to>` where `<to>` is the name of the `sunsetto` page.

#### Example Blueprint

The following would be added to a template normally use by a page that would be under control, c.f. `embargoCheckWhenTemplateIs` and `embargoCheckWhenParentIs`.

The `skipembargo` section is optional, and you would only add this if you wanted to be able to explicitly uncontrol a page from an emnbargo check (sunrise).

The `sunset` and `sunsetto` elements are mandatory for sunset (withdrawal) to work.

```yaml
skipembargo:
  type: toggle
  default: off
  text:
    - Embargo until publication date
    - Uncontrolled
  label: Release Control
sunset:
  type: date
  time: true
  label: Withdraw (Sunset) at
sunsetto:
  type: select
  label: Redirect after withdrawn (Sunset) to
  options: query
  query:
    fetch: site.pages
```

#### Example Use in a collection

In the kirby3 starterkit you don't want the list of blog post to include any that are under embaego (waiting sunrise) or are sunset... it is very simple to implement.

`site/collections/articles.php` looks like this:

```php
<?php

return function ($site) {
    return $site->find('blog')->children()->listed()->flip();
};
```

Change it to:
```php
<?php

return function ($site) {
	    return $site->find('blog')->children()->listed()->isunderembargo(false)->issunset(false)->flip();
};
```

Note the use of `false` in the filters for `isunderembargo` and `issunset` because its the pages that are NOT in this condition that are required.

#### Methods

This plugin makes the following methods available:

- `page.issunset()` : returns a boolean which is `true` if the page is currently sunset (i.e. withdrawn).
- `page.isunderembargo()` : returns a boolean which is `true` if the page is currently under embargo (i.e. waiting for sunrise).

- `pages.issunset( $match = true )` : a filter to return the subset of pages in a collection that are ( `$match = true` ) or are not ( `$match = false` ) sunset (i.e. withdrawn).
- `pages.isunderembargo( $match = true )` : a filter to return the subset of pages in a collection that are ( `$match = true` ) or are not ( `$match = false` ) under embargo (i.e. waiting for sunrise).

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/omz13/kirby3-suncyclepages/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

You are prohibited from using this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

### Buy Me A Coffee

To show your support for this project you are welcome to [buy me a coffee](https://buymeacoff.ee/omz13).

<!-- If you are using this plugin on a kirby3 site that has a Personal licence, to show your support for this project you are welcome to [buy me a coffee](https://buymeacoff.ee/omz13).

If you are using this plugin with a kirby3 site that has a Pro licence, to show your support for this project you are greatly encouraged to [buy me a coffee](https://buymeacoff.ee/omz13).
-->

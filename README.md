# Kirby3 SunCyclePages

**Requirement:** Kirby 3

## Documentation

### Purpose

For a kirby3 site, this plugin [omz13/suncyclepages](https://github.com/omz13/kirby3-suncyclepages) allows the lifecycle of page to be controlled vis-à-vis pages becoming generally available at a sunrise date and withdrawn at a sunset date.

- Only pages that have a status of "published" are affected, i.e. those with "draft" or "unpublished" behave as usual.
- Pages can be embargoed until being made generally available a specified date ("sunrise"). Any attempt to view the page before that date will yield the standard error page and a 404 [Not Found] code; note that if Kirby is in debug mode, a 417 is given instead.
- Pages can be withdrawn at a specific date ("sunset"). Any attempt to view the page after that date will yield a 410 [Gone] and the standard error page, or a 301 [Moved Permanently] to a specified location. 

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

- `omz13.suncyclepages.disable` : a boolean which, if true, disables the plugin.

### Use

#### Content fields

The plugin uses the following content fields. These are all optional; if missing or empty, they are assumed to be not applicable vis-à-via the indicated functionality.

- `embargo` : a boolean which, if true, indicates that the page should be under embargo until the date specified in the `date` field.
- `sunset` : a date which, if specified, is the date after which the page is to be sunset (withdrawn).
- `sunsetto` : the name of a page to be used for redirects when the page is sunset

#### Example Blueprint

```yaml
embargo:
  type: toggle
  default: off
  text:
    - Uncontrolled
    - Embargo until publication date
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

#### Methods

This plugin makes the following methods available:

- `page.issunset()` : returns a boolean which is `true` if the page is currently sunset (i.e. withdrawn).
- `page.isunderembargo()` : returns a boolean which is `true` if the page is currently under embargo (i.e. waiting for sunrise).

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

# Angular template support for dc/bundler

Enables you to minify Javascript files using dc/bundler.

## Installation

```
composer require dc/bundler-ngtpl
```

or in `composer.json`

```json
"require": {
    "dc/bundler-ngtpl": "dev-master"
}
```

## Setup

This package depends on `dc/router`, but strongly suggests `dc/ioc`. This is how you register the transformer with
the IoC container so it is picked up automatically:

```php
\DC\Bundler\NGTemplate\NGTemplateTransformer::registerWithContainer($container, "module");
```

Specify the Angular module whose `$templateCache` will register the models.
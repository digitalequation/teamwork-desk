# PHP (Laravel) Teamwork Desk (Internal Use)

[![Actions Status](https://github.com/digitalequation/teamwork-desk/workflows/Run%20Tests/badge.svg)](https://github.com/digitalequation/teamwork-desk/actions)

## Installation

You can install the package via composer:

```bash
composer require digitalequation/teamwork-desk
```

After the installation is complete, from your project's root run:
```bash
php artisan teamwork-desk:install --force
```

This will publish all the files of the package:
- migrations
- factories
- config file
- service provider

## Usage

Available commands:
**NOTE:** passing `--force` to any command will overwrite the already published files.
``` php
# Publish the config file
php artisan teamwork-desk:config

# Publish the database factories 
php artisan teamwork-desk:factories

# Publish the database migrations
php artisan teamwork-desk:migrations
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email robert.chiribuc@thebug.ro instead of using the issue tracker.

## Credits

- [Robert Cristian Chiribuc](https://github.com/chiribuc)
- [All Contributors](../../contributors)

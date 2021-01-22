# PHP (Laravel) Teamwork Desk v2 (Internal Use)

[![Actions Status](https://github.com/digitalequation/teamwork-desk/workflows/Run%20Tests/badge.svg)](https://github.com/digitalequation/teamwork-desk/actions)

<h3><span style="color:red">For version 1 of the package check the [v1.md](./v1.md) documentation.</span></h3>

## Installation

You can install the package via composer:

```bash
composer require digitalequation/teamwork-desk
```

After the installation is complete, publish the migrations files:
```bash
php artisan vendor:publish --provider="DigitalEquation\TeamworkDesk\TeamworkDeskServiceProvider" --tag="migrations"

php artisan migrate
```

Publish the config file:
```bash
php artisan vendor:publish --provider="DigitalEquation\TeamworkDesk\TeamworkDeskServiceProvider" --tag="config"
```


The package will throw two events, `SupportTicketCreated` and `SupportTicketWebhookReceived` that you can listen to using an event listener subscriber and process the data as you see fit.

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

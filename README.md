# Dotkernel Frontend

Dotkernel Frontend is an application (skeleton) based on Mezzio microframework using Laminas components.
It's designed as a web starter package suitable for frontend applications.
The current functionality is included as a proof of concept and to showcase Frontend's file architecture:

- Contact us page
- Generic page with copy
- User accounts

Use these building blocks as an example for your own custom code.

> Check out our [demo](https://v5.dotkernel.net/).

## Documentation

Documentation is available at: https://docs.dotkernel.org/frontend-documentation/

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/frontend)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/frontend/5.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/frontend)](https://github.com/dotkernel/frontend/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/frontend)](https://github.com/dotkernel/frontend/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/frontend)](https://github.com/dotkernel/frontend/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/frontend)](https://github.com/dotkernel/frontend/blob/5.0/LICENSE.md)

[![Continuous Integration](https://github.com/dotkernel/frontend/actions/workflows/continuous-integration.yml/badge.svg?branch=5.0)](https://github.com/dotkernel/frontend/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/frontend/graph/badge.svg?token=BQS43UWAM4)](https://codecov.io/gh/dotkernel/frontend)
[![Qodana](https://github.com/dotkernel/frontend/actions/workflows/qodana_code_quality.yml/badge.svg)](https://github.com/dotkernel/frontend/actions/workflows/qodana_code_quality.yml)
[![PHPStan](https://github.com/dotkernel/frontend/actions/workflows/static-analysis.yml/badge.svg?branch=5.0)](https://github.com/dotkernel/frontend/actions/workflows/static-analysis.yml)

## Installing Dotkernel `frontend`

- [Installing Dotkernel `frontend`](#installing-dotkernel-frontend)
    - [Composer](#composer)
    - [Choose a destination path for Dotkernel `frontend` installation](#choosing-an-installation-path-for-dotkernel-frontend)
    - [Installing Dotkernel Frontend](#installing-dotkernel-frontend)
    - [Configuration - First Run](#configuration---first-run)
    - [Testing (Running)](#running-the-application)

## Tools

Dotkernel Frontend interface has been tested with npm v10.0.4 and Node.js v20.11.0.

### Composer

Installation instructions:

- [Composer Installation -  Linux/Unix/OSX](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
- [Composer Installation - Windows](https://getcomposer.org/doc/00-intro.md#installation-windows)

> If you have never used composer before make sure you read the [`Composer Basic Usage`](https://getcomposer.org/doc/01-basic-usage.md) section in Composer's documentation

## Choosing an installation path for Dotkernel `frontend`

Example:

- absolute path `/var/www/dk`
- or relative path `dk` (equivalent with `./dk`)

## Installing Dotkernel `frontend`

After you choose the path for Dotkernel Frontend (`dk` will be used for the remainder of this example), let's move onto installation.

### Note

The installation uses the PHP extension `ext-intl` that may not be enabled by default in your web server. If the installation returns a similar error to the below, check the `extension=intl` extension in your `php.ini`.

```shell
Your requirements could not be resolved to an installable set of packages.

Problem 1
 - laminas/laminas-i18n 2.10.3 requires ext-intl * -> the requested PHP extension intl is missing from your system.
```

To enable an extension, remove the semicolon (;) in front of it.

#### Installing Dotkernel `frontend` using git clone

This method ensures that the default branch is installed, even if it is not released. Run the following command:

```shell
git clone https://github.com/dotkernel/frontend.git .
```

The dependencies have to be installed separately, by running this command:

```shell
composer install
```

The setup script prompts for some configuration settings, for example the lines below:

```shell
Please select which config file you wish to inject 'Laminas\Diactoros\ConfigProvider' into:
  [0] Do not inject
  [1] config/config.php
  Make your selection (default is 1):
```

Simply select `[0] Do not inject`, because Dotkernel includes its own configProvider which already contains the prompted configurations.

If you choose `[1] config/config.php` Laminas's `ConfigProvider` from `session` will be injected.

The next question is:

`Remember this option for other packages of the same type? (y/N)`

## Configuration - First Run

- duplicate `config/autoload/development.local.php.dist` as `config/autoload/development.local.php`
- duplicate `config/autoload/local.php.dist` as `config/autoload/local.php`
- duplicate `config/autoload/mail.local.php.dist` as `config/autoload/mail.local.php`
- Edit `config/autoload/local.php` according to your dev machine and fill in the `database` configuration.

## Configuration - Mail (optional)

If you want your application to send mails on registration, contact etc. add valid credentials to the following keys in `config/autoload/mail.local.php`

Under `message_options` key:

- `from` - email address that will send emails (required)
- `from_name` - organization name for signing sent emails (optional)

Under `smtp_options` key:

- `host` - hostname or IP address of the mail server (required)
- `connection_config` - add the `username` and `password` keys (required)

In `config/autoload/local.php` edit the key `contact` => `message_receivers` => `to` with *string* values for emails that should receive contact messages.

Note: **Please add at least 1 email address in order for contact message to reach someone**

Also feel free to add as many CCs as you want under the `contact` => `message_receivers` => `cc` key.

## Configuration - reCAPTCHA (optional)

reCAPTCHA is used to prevent abusive activities on your website. Dotkernel frontend uses the Google reCAPTCHA for its contact us form.
You must first generate a `siteKey` and `secretKey` in your Google account - [Google reCAPTCHA](https://www.google.com/recaptcha/admin)

Update the `recaptcha` array in `config/autoload/local.php` with the `siteKey` and `secretKey` from Google reCAPTCHA.

Note: you need to whitelist `localhost` in the reCAPTCHA settings page during development.
**When in production do not forget to either remove `localhost` from the reCAPTCHA whitelist, or have a separate reCAPTCHA**

## Database migration

Running the database migration is done with this command

```shell
php vendor/bin/doctrine-migrations migrate
```

Note: if you have already run the phinx migrations, you may get this message

```shell
WARNING! You have x previously executed migrations in the database that are not registered migrations.
  {migration list}
Are you sure you wish to continue? (y/n)
```

After submitting `y`, you will get this confirmation message.

```shell
WARNING! You are about to execute a database migration that could result in schema changes and data loss. Are you sure you wish to continue? (y/n)
```

Again, submit `y` to run all the migrations in chronological order. Each migration will be logged in the `migrations` table to prevent running the same migration more than once, which is often not desirable.

## Seeding the database (Fixtures)

Seeding the database is done with the help of our custom package ``dotkernel/dot-data-fixtures`` built on top of doctrine/data-fixtures. To execute all fixtures, run:

```shell
php bin/doctrine fixtures:execute
```

## Development mode

Run this command to enable dev mode by turning debug flag to `true` and turning configuration caching to `off`. It will also make sure that any existing config cache is cleared.

```shell
composer development-enable
```

- If not already done, remove the `.dist` extension from `config/autoload/development.global.php.dist`.

## NPM Commands

To install dependencies into the `node_modules` directory run this command.

```shell
npm install
```

- If `npm install` fails, this could be caused by user permissions of npm. Recommendation is to install npm through `Node Version Manager`.

The watch command compiles the components then watches the files and recompiles when one of them changes.

```shell
npm run watch
```  

After all updates are done, this command compiles the assets locally, minifies them and makes them ready for production.

```shell
npm run prod
```

## Running the application

We recommend running your applications in WSL:

- make sure you have [WSL](https://github.com/dotkernel/development/blob/main/wsl/README.md) installed on your system
- currently we provide a distro implementations for [AlmaLinux9](https://github.com/dotkernel/development/blob/main/wsl/os/almalinux9/README.md)
- install the application in a virtualhost as recommended by the chosen distro
- set `$baseUrl` in **config/autoload/local.php** to the address of the virtualhost
- run the application by opening the virtualhost address in your browser

You should see the `Dotkernel Frontend` welcome page.

**NOTE:**

- If you are getting exceptions or errors regarding some missing services, try running the following command:

```shell
sudo php bin/clear-config-cache.php
```

> If `config-cache.php` is present that config will be loaded regardless of the `ConfigAggregator::ENABLE_CACHE` in `config/autoload/mezzio.global.php`

- **Development only**: `session.cookie_secure` does not work locally so make sure you modify your `local.php`, as per the following:

```php
# other code

return [
    # other configurations...
    'session_config' => [
        'cookie_secure' => false,
    ],
];
```

Do not change this in `local.php.dist` as well because this value should remain `true` on production.

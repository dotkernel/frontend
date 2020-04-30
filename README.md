# frontend

Dotkernel web starter package suitable for frontend applications.

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/frontend)](https://github.com/dotkernel/frontend/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/frontend)](https://github.com/dotkernel/frontend/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/frontend)](https://github.com/dotkernel/frontend/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/frontend)](https://github.com/dotkernel/frontend/blob/3.0/LICENSE.md)


![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/frontend/3.0.x-dev)


# Installing DotKernel `frontend`

- [Installing DotKernel `frontend`](#installing-dotkernel-frontend)
    - [Installation](#installation)
        - [Composer](#composer)
    - [Choose a destination path for DotKernel `frontend` installation](#choose-a-destination-path-for-dotkernel-frontend-installation)
    - [Installing the `frontend` Composer package](#installing-the-frontend-composer-package)
        - [Installing DotKernel Frontend](#installing-dotkernel-frontend)
    - [Configuration - First Run](#configuration---first-run)
    - [Testing (Running)](#testing-running)

## Tools

DotKernel can be installed through a single command that utilizes [Composer](https://getcomposer.org/). Because of that, Composer is required to install DotKernel `frontend`.

### Composer

Installation instructions:

- [Composer Installation -  Linux/Unix/OSX](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
- [Composer Installation - Windows](https://getcomposer.org/doc/00-intro.md#installation-windows)

> If you have never used composer before make sure you read the [`Composer Basic Usage`](https://getcomposer.org/doc/01-basic-usage.md) section in Composer's documentation

## Choosing an installation path for DotKernel `frontend` 

Example:

- absolute path `/var/www/dk`
- or relative path `dk` (equivalent with `./dk`)

## Installing DotKernel `frontend`

After choosing the path for DotKernel (`dk` will be used for the remainder of this example) it must be installed. There are two installation methods. 

#### I. Installing DotKernel `frontend` using composer 

The advantage of using this command is that it runs through the whole installation process. Run the following command:

```bash
$ composer create-project dotkernel/frontend -s dev dk
```

The above command downloads the `frontend` package, then downloads and installs the `dependencies`.

The setup script prompts for some configuration settings, for example the lines below:

```shell
Please select which config file you wish to inject 'Laminas\Diactoros\ConfigProvider' into:
  [0] Do not inject
  [1] config/config.php
  Make your selection (default is 0):
```

Simply select `[0] Do not inject`, because DotKernel includes its own configProvider which already contains the prompted configurations.
If you choose `[1] config/config.php` Laminas's `ConfigProvider` from `session` will be injected.

The next question is:

`Remember this option for other packages of the same type? (y/N)`

Type `y` here, and hit `enter`

#### II. Installing DotKernel `frontend` using git clone

This method requires more manual input, but it ensures that the default branch is installed, even if it is not released. Run the following command:

```bash
$ git clone https://github.com/dotkernel/frontend.git .
```

The dependencies have to be installed separately, by running this command
```bash
$ composer install
```

Just like for `II Installing DotKernel frontend using composer` (see above), the setup asks for configuration settings regarding injections (type `0` and hit `enter`) and a confirmation to use this setting for other packages (type `y` and hit `enter`)

## Configuration - First Run

- Remove the `.dist` extension from the files `config/autoload/local.php.dist` and `config/autoload/mail.local.php.dist`
- Edit `config/autoload/local.php` according to your dev machine and fill in the `database` configuration 
- Add smtp credentials in `config/autoload/mail.local.php` if you want your application to send mails on registration etc.
- Run the [migrations](../Overview/Migrations.md) with this command:

```bash
php vendor/bin/phinx migrate --config="config/migrations.php"
```
- [Get a recaptcha key pair](https://www.google.com/recaptcha/admin) and add them in `config/autoload/local.php`
- If you use `composer create-project`, the project will go into development mode automatically after installing. The development mode status can be checked and toggled by using these composer commands

```bash
$ composer development-status
$ composer development-enable
$ composer development-disable
```

- If not already done on installation, remove the `.dist` extension from `config/autoload/development.global.php.dist`.
This will enable dev mode by turning debug flag to `true` and turning configuration caching to `off`. It will also make sure that any existing config cache is cleared.

> Charset recommendation: utf8mb4_general_ci

## Testing (Running)

Note: **Do not enable dev mode in production**

- Run the following command in your project's directory to start PHPs built-in server:

```bash
$ php -S 0.0.0.0:8080 -t public
```

> Running command `composer serve` will do the exact same, but the above is faster.

`0.0.0.0` means that the server is open to all incoming connections
`127.0.0.1` means that the server can only be accessed locally (localhost only)
`8080` the port on which the server is started (the listening port for the server)

**NOTE:**
If you are still getting exceptions or errors regarding some missing services, try running the following command

```php
php bin/clear-config-cache.php
```

> If `config-cache.php` is present that config will be loaded regardless of the `ConfigAggregator::ENABLE_CACHE` in `config/autoload/mezzio.global.php`

- Open a web browser and visit `http://localhost:8080/`

You should see the `DotKernel Frontend` welcome page.

# frontend

Dotkernel web starter package suitable for frontend applications.

## Requirements
* Frontend application require PHP >= 7.1
* In order to use the default setup and import the database files, you need MySQL

## Installation

Create a new project directory and change dir to it. Run the following composer command
```bash
$ composer create-project dotkernel/frontend .
```

The setup script will prompt for some custom settings. When encountering a question like the one below:

```shell
Please select which config file you wish to inject 'Zend\Session\ConfigProvider' into:
  [0] Do not inject
  [1] config/config.php
  Make your selection (default is 0):
```

For this option select `[0] Do not inject` because the frontend application  already has an injected config provider which already contains the prompted configurations.

`Remember this option for other packages of the same type? (y/N)`
`y`
The `ConfigProvider`'s can be left un-injected as the requested configurations are already loaded.

## Configuration

* import the database schema, if you are using mysql, which can be found in `data/frontend.sql`
* remove the `.dist` extension of the file `local.php.dist` located in `config/autoload`
* edit `local.php` according to your dev machine. Fill in the `database` configuration and smtp credentials if you want your application to send mails on registration etc.
* get a recaptcha key pair and configure the `local.php` with them
* if you use the create-project command, after installing, the project will go into development mode automatically
* you can also toggle development mode by using the composer commands
```bash
$ composer development-enable
$ composer development-disable
```
* if not already done on installation, copy file `development.global.php.dist` to `development.global.php`

This will enable dev mode by turning debug flag to true and turning configuration caching off. It will also make sure that any previously config cache is cleared.

**Do not enable dev mode in production**

* Next, run the following command in your project's directory
```bash
$ php -S 0.0.0.0:8080 -t public
```
* visit `http://localhost:8080` in your browser

**NOTE:**
If you are still getting exceptions or errors regarding some missing services, try running the following command
```bash
$ composer clear-config-cache
```

**NOTE**
If you get errors when running composer commands like development-enable or clear-config-cache related to parsing errors and strict types
it is probably because you don't have the PHP CLI version > 7.1 installed

If you cannot use these commands(for example if you cannot upgrade PHP globally) you can setup/clean the project by hand as described below or if you have a locally installed PHP 7.1 version you can use that
* enable development mode by renaming the files `config/development.config.php.dist` and `config/autoload/development.local.php.dist` to have the `.dist` extension removed
* disable dev mode by reverting the above procedure
* manually clear cached data from `data/cache` directory and optionally `data/proxies`


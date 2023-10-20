# frontend

Dotkernel web starter package suitable for frontend applications.


![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/frontend)


[![GitHub issues](https://img.shields.io/github/issues/dotkernel/frontend)](https://github.com/dotkernel/frontend/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/frontend)](https://github.com/dotkernel/frontend/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/frontend)](https://github.com/dotkernel/frontend/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/frontend)](https://github.com/dotkernel/frontend/blob/4.0/LICENSE.md)


![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/frontend/4.0.0)

[![Build Static](https://github.com/dotkernel/frontend/actions/workflows/static-analysis.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/frontend/actions/workflows/static-analysis.yml)
[![Build Static](https://github.com/dotkernel/frontend/actions/workflows/run-tests.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/frontend/actions/workflows/run-tests.yml)
[![codecov](https://codecov.io/gh/dotkernel/frontend/graph/badge.svg?token=BQS43UWAM4)](https://codecov.io/gh/dotkernel/frontend)

[![SymfonyInsight](https://insight.symfony.com/projects/a28dac55-3366-4020-9a49-53f6fcbeda4e/big.svg)](https://insight.symfony.com/projects/a28dac55-3366-4020-9a49-53f6fcbeda4e)

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

#### Note
The installation uses the PHP extension `ext-intl` that may not be enabled by default in your web server. If the installation returns a similar error to the below, check the `extension=intl` extension in your `php.ini`.

```bash
Your requirements could not be resolved to an installable set of packages.

Problem 1
 - laminas/laminas-i18n 2.10.3 requires ext-intl * -> the requested PHP extension intl is missing from your system.
```

To enable an extension, remove the semicolon (;) in front of it.

#### I. Installing DotKernel `frontend` using composer 

The advantage of using this command is that it runs through the whole installation process. Run the following command:

    composer create-project dotkernel/frontend -s dev dk

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

    git clone https://github.com/dotkernel/frontend.git .

The dependencies have to be installed separately, by running this command:

    composer install

Just like for `II Installing DotKernel frontend using composer` (see above), the setup asks for configuration settings regarding injections (type `0` and hit `enter`) and a confirmation to use this setting for other packages (type `y` and hit `enter`)

## Configuration - First Run

- Remove the `.dist` extension from the files `config/autoload/local.php.dist` and `config/autoload/mail.local.php.dist`
- Edit `config/autoload/local.php` according to your dev machine and fill in the `database` configuration

## Configuration - Mail
 
If you want your application to send mails on registration, contact... please provide valid credentials to the following keys in `config/autoload/mail.local.php`

Under `message_options` key:
- `from` - email address from whom users will receive emails (required)
- `from_name` - organization name from whom users will receive emails (optional)

Under `smtp_options` key:
- `host` - hostname or IP address of the mail server (required)
- `connection_config` - please complete the `username` and `password` keys (required)

In `config/autoload/local.php` add under `contact` => `message_receivers` => `to` key *string* values with the emails that should receive contact messages

Note: **Please add at least 1 email address in order for contact message to reach someone**

Also feel free to add as many cc as you want under `contact` => `message_receivers` => `cc` key

## Configuration - reCAPTCHA

reCAPTCHA is used to prevent abusive activities on your website. DotKernel frontend uses the Google reCAPTCHA for its contact us form.
You must first generate a `siteKey` and `secretKey` in your Google account - [Google reCAPTCHA](https://www.google.com/recaptcha/admin)

Update the `recaptcha` array in `config/autoload/local.php` with the `siteKey` and `secretKey` from Google reCAPTCHA.

Note: you need to whitelist `localhost` in the reCAPTCHA settings page during development.
**When in production do not forget to either remove `localhost` from the reCAPTCHA whitelist, or have a separate reCAPTCHA**


## Migrations

Out of the box, we use Doctrine Migrations like detailed below to populate the database. An example file is included in `/data/doctrine/migrations`. To generate a new migration file, use this command:

```bash
php vendor/bin/doctrine-migrations migrations:generate
```
It creates a PHP file like this one `/data/doctrine/migrations/Version20220606131835.php` that can then be edited in the IDE. You can add new queries to be executed when the migration is run (in `public function up`) and optionally queries that undo those changes (in `public function down`).

Here is an example you can add in `public function up` 
```bash
$this->addSql('ALTER TABLE users ADD test VARCHAR(255) NOT NULL');
```
and its opposite in `public function down`
```bash
$this->addSql('ALTER TABLE users DROP test');
```

Running the migrations is done with this command
```bash
php vendor/bin/doctrine-migrations migrate
```
Note: if you have already run the phinx migrations, you may get this message
```bash
WARNING! You have x previously executed migrations in the database that are not registered migrations.
  {migration list}
Are you sure you wish to continue? (y/n)
```
After submitting `y`, you will get this confirmation message.
```bash
WARNING! You are about to execute a database migration that could result in schema changes and data loss. Are you sure you wish to continue? (y/n)
```
Again, submit `y` to run all of the migrations in chronological order. Each migration will be logged in the `migrations` table to prevent running the same migration more than once, which is often not desirable.

You can opt to run a single migration
```bash
php vendor/bin/doctrine-migrations migrations:execute --up 20220606131835
```
and you can revert its changes with
```bash
php vendor/bin/doctrine-migrations migrations:execute --down 20220606131835
```
This will also remove the log for that migration in the database, allowing the migration to run again with `php vendor/bin/doctrine-migrations migrate`. 
Note the `20220606131835` is taken from the migration filename, e.g. `Version20220606131835.php`

## Seeding the database (Fixtures)
Seeding the database is done with the help of our custom package ``dotkernel/dot-data-fixtures`` built on top of doctrine/data-fixtures. See below on how to use our CLI command for listing and executing Doctrine data fixtures.

An example of a fixtures class is ``data/doctrine/fixtures/RoleLoader.php``

To list all the available fixtures, by order of execution, run:

    php bin/doctrine fixtures:list

To execute all fixtures, run:

    php bin/doctrine fixtures:execute

To execute a specific fixtures, run:

    php bin/doctrine fixtures:execute --class=RoleLoader

Fixtures can and should be ordered to ensure database consistency, more on ordering fixtures can be found here :
https://www.doctrine-project.org/projects/doctrine-data-fixtures/en/latest/how-to/fixture-ordering.html#fixture-ordering

## Development mode

- If you use `composer create-project`, the project will go into development mode automatically after installing. The development mode status can be checked and toggled by using these composer commands

```bash
composer development-status
composer development-enable
composer development-disable
```

- If not already done on installation, remove the `.dist` extension from `config/autoload/development.global.php.dist`.
This will enable dev mode by turning debug flag to `true` and turning configuration caching to `off`. It will also make sure that any existing config cache is cleared.

> Charset recommendation: utf8mb4_general_ci

## Using DebugBar

DotKernel comes with its own DebugBar already installed and configured, but disabled by default.

In order to enable it, you need to clone the config file `config/autoload/debugbar.local.php.dist` as `config/autoload/debugbar.local.php`.

More about DebugBar [here](https://github.com/dotkernel/dot-debugbar).

## Email Templates

These are the email templates provided on a fresh installation, all present in the User module

- `activate.html.twig` used for the activation email
- `reset-password-requested.html.twig` used when the user requests a password reset
- `reset-password-completed.html.twig` used when the password has reset successfully

## NPM Commands

To install dependencies into the `node_modules` directory run this command.
```bash
npm install
``` 
- If `npm install` fails, this could be caused by user permissions of npm. Recommendation is to install npm through `Node Version Manager`.

The watch command compiles the components then watches the files and recompiles when one of them changes.

```bash
npm run watch
```  

After all updates are done, this command compiles the assets locally, minifies them and makes them ready for production. 

```bash
npm run prod
```
## Authorization Guards
The packages responsible for restricting access to certain parts of the application are [dot-rbac-guard](https://github.com/dotkernel/dot-rbac-guard) and [dot-rbac](https://github.com/dotkernel/dot-rbac). These packages work together to create an infrastructure that is customizable and diversified to manage user access to the platform by specifying the type of role the user has.

The `authorization.global.php` file provides multiple configurations specifying multiple roles as well as the types of permissions to which these roles have access.

```php
//example of a flat RBAC model that specifies two types of roles as well as their permission
    'roles' => [
                'admin' => [
                    'permissions' => [
                        'authenticated',
                        'edit',
                        'delete',
                        //etc..
                    ]
                ],
                'user' => [
                    'permissions' => [
                        'authenticated',
                        //etc..
                    ]
                ]
            ]
```

The `authorization-guards.global.php` file provides configuration to restrict access to certain actions based on the permissions defined in `authorization.global.php` so basically we have to add the permissions in the dot-rbac configuration file first to specify the action restriction permissions.

```php
// configuration example to restrict certain actions of some routes based on the permissions specified in the dot-rbac configuration file
    'rules' => [
                    [
                        'route' => 'account',
                        'actions' => [//list of actions to apply , or empty array for all actions
                            'unregister',
                            'avatar',
                            'details',
                            'changePassword'
                        ],
                        'permissions' => ['authenticated']
                    ],
                    [
                        'route' => 'admin',
                        'actions' => [
                            'deleteAccount'
                        ],
                         'permissions' => [
                            'delete'
                            //list of roles to allow
                        ]
                    ]
                ]
```

## Languages

The `local.php.dist` file provides an example for working with multiple languages. The `translator` variable can be expanded to other languages using [Poedit](https://poedit.net/) which can edit `.po` files like the example in `data/language/da_DK/LC_MESSAGES/messages.po`. The compiled file will have the extension `.mo`

To apply the translations 
- the twig templates need either `{% trans 'translateText' %}` or `{{ translateText|trans }}`
- then the js file needs `translateText("translateText")`

**NOTE:**
In order to have a proper behaviour of language selector , you need the language pack installed at Operating System level.

`dnf install glibc-all-langpacks`

Then restart PHP-FPM. 


## Running the application
We recommend running your applications in WSL:
* make sure you have [WSL](https://github.com/dotkernel/development/blob/main/wsl/README.md) installed on your system
* currently we provide 2 distro implementations: [AlmaLinux9](https://github.com/dotkernel/development/blob/main/wsl/os/almalinux9/README.md) and [Ubuntu20](https://github.com/dotkernel/development/blob/main/wsl/os/ubuntu20/README.md)
* install the application in a virtualhost as recommended by the chosen distro
* set `$baseUrl` in **config/autoload/local.php** to the address of the virtualhost
* run the application by opening the virtualhost address in your browser

You should see the `DotKernel Frontend` welcome page.


**NOTE:**
- If you are getting exceptions or errors regarding some missing services, try running the following command:

    php bin/clear-config-cache.php

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

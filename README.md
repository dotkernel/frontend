# dot-frontend

Dotkernel web starter package suitable for frontend applications.

## Installation

Create a new project directory and change dir to it. Run the following composer command
```bash
$ composer create-project -s dev dotkernel/dot-frontend .
```

## Configuration

* import the database schema, if you are using mysql, found in `data/dot-frontend.sql`
* remove the `.dist` extension of the files `local.php.dist` and `errorhandler.local.php.dist` located in `config/autoload`
* edit `local.php` according to your dev machine. Fill in the `database` configuration and a smtp credentials if you want your application to send mails on registration etc.
* get a recaptcha key pair and configure the `local.php` with them
* run the following command in your project root dir
```bash
$ composer development-enable
```
This will enable dev mode having debug flag true and configuration caching off. It also make sure that any previously config cache is cleared.

**Do not enable dev mode in production**

* Next, run the following command in your project's directory
```bash
$ php -S 0.0.0.0:8080 -t public
```
* visit `http://localhost:8080` in your browser

**NOTE:**
If you still get exceptions or errors regarding some missing services, try running the following command
```bash
$ composer clear-config-cache
```

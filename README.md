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
* run the following command in your project's directory
```bash
$ php -S 0.0.0.0:8080 -t public
```
* visit `http://localhost:8080` in your browser

**NOTE:**
If you get exceptions or errors regarding some missing services, check folder `data/cache` and make sure to remove any file found there.
You probably run the application before activating `local.php` in which case config cache is enabled. We need to disable it for development



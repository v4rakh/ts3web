# README

**ts3web** is a web interface for any TeamSpeak 3 Server used with serverQuery login.
 
This web interface aims to be as simple as possible. The minimalistic approach is intentional.

If you like to help (to translate or implement features), open an issue first. If possible, you should use existing code to implement new features. PRs will be merged after a code review.

Features which are currently not supported:
- Permissions Management (except for viewing)
- File Management (except for viewing)

## Install / Deployment

You can either use docker or manual deployment.

### Docker

#### Install

There's an example `docker-compose.yml` in the `docker-compose/` directory. Please read the following section carefully as the setup will be explained.

* Clone repository
* Copy `config/env.example` to `config/env` and adjust to your needs. Ensure that if you share the same docker network, the environment variable `teamspeak_default_host` should be the name of your teamspeak docker container.
* Build the docker image from the project home with `docker build -t ts3web:latest -f docker/Dockerfile .`
* Create a container with the image. Make sure that if teamspeak and ts3web share the same docker instance they should be put into one network and the subnet should be added to teamspeak's query whitelist. The web interface won't work otherwise.

From the example:

* Copy `docker-compose/env` file to `config/env`
* Build the docker image from the project home with `docker build -t ts3web:latest -f docker/Dockerfile .`
* Change directory to `docker-compose/` folder
* Execute `docker-compose up -d`
* Everything should be up and running. Point your browser to `8181` to see the web interface, look into the logs for a teamspeak serveradmin login.

#### Upgrade

* Change directory to project home
* `git pull`
* Change directory to `docker-compose/` folder
* Execute `docker-compose down`
* Execute `docker-compose up -d`

### Manual

#### Install

* Clone repository
* Install composer
* Change directory to project home
* `composer install`

#### Configuration

* Copy `config/env.example` to `config/env` and adjust to your needs
* Configure nginx or apache.
    * Point your document root to `public/`.
    * Example `nginx.conf`:

        ```  
        root   .../public;
        index index.php;    
        
        rewrite_log on;
        
        location / {
          try_files $uri $uri/ @ee;
        }
        
        location @ee {
          rewrite ^(.*) /index.php?$1 last;
        }
        
        # php fpm
        location ~ \.php$ {
          fastcgi_split_path_info ^(.+\.php)(/.+)$;
          fastcgi_pass   unix:/var/run/php-fpm/php-fpm.sock;
          include        fastcgi_params;
        }
        ```
    
#### Upgrade

* Change directory to project home
* `git pull`
* `composer update`

## Developers
* start server with `php -S localhost:8080 -t public public/index.php`
* point browser to [localhost:8080](http://localhost:8080) to have a preview

### Helpers

Attributes can be defined when including `table`, `keyvalues` and `form` templates of twig. This helps to generate tables and forms without the need to specify all attributes.

```
hiddenDependingOnAttribute // hides a row depending on a value in a table
hiddenColumns // hides an entire column depending on a key in a table
links // generates a link for a specific cell in a table or keyvalue
additional_links // generates extra columns in a table
filters // applies filters depending on a key in a table or key value view
attributesEditable // define editable attributes in the key value view
fields // define fields for a form
```

See example usage in the folder `View/material`.

## Translations
- This app uses Symfony Translator. It's bootstrapped in `Util\BootstrapHelper` and locales are placed under `data/locale/`. Adjust to your needs or help translating.
- Form fields (name/id should be the same) are also translated. For a field named `content` or `ConT enT` translate `form_field_content`.


## Theme
Themes can be chosen in the `env` file by editing the `theme` variable. Templates are mapped to the corresponding view folder in `src/View/<themeName>`. `.css`, `.js` and other style files like `.ttf` or `.woff2` for fonts should be placed in `public/theme/<themeName>` and accessed accordingly. See an example in `src/View/material/layout/header.twig`.
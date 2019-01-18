# README

**ts3web** is a web interface for any TeamSpeak 3 Server used with serverQuery login.
 
This web interface aims to be as simple as possible. The minimalistic approach is intentional.

If you like to help (to translate or implement features), open an issue first. If possible, you should use existing code to implement new features. PRs will be merged after a code review.

Features which are currently not supported:

* Permissions Management (except for viewing)
* File Management (except for viewing)

## Configuration

The main configuration file is the `env` file located in `config/`. There's an example file called `env.example` which you
need to copy to `config/env`. 

**Information for docker users**: it's possible to have the `env` file persistent and not within the container so 
that rebuilding the image isn't required. Create a host volume `/path/from/host/env` and map it to 
`/var/www/html/application/config/env`. Changes will show up after restarting the container.

## Install / Deployment

You can either use docker or manual deployment. Please read the following section carefully.

### Automatic installation and deployment with docker or docker-compose

##### Install

###### docker

* Clone repository
* Copy either `config/env.example` to `config/env` and adjust to your needs or make it persistent outside if the container. To do so take a look at the configuration section first). Ensure that if you share the same docker network, the environment variable `teamspeak_default_host` should be the name of your teamspeak docker container.
* Build the docker image from the project home with `docker build -t teamspeak_web:latest -f docker/Dockerfile .` 
* Create a container with the image, e.g. `docker run --name teamspeak_web -p 8181:80 slimblog_app:latest`. Make sure that if teamspeak and ts3web share the same docker instance they should be put into one network and the subnet **needs be added to teamspeak's query whitelist**. The web interface won't work otherwise.
* Point your browser to `8181` to see the web interface. 

###### docker-compose

There's an example `docker-compose.yml` in the `docker-compose/` directory. 

* Adjust `docker-compose/env` if you need to change something.
* Build the docker image from the project home with `docker build -t teamspeak_web:latest -f docker/Dockerfile .`
* Change directory to `docker-compose/` folder
* Execute `docker-compose up -d`
* Point your browser to `8080` to see the web interface. 

##### Upgrade

###### docker

* Change directory to project home
* `git pull`
* Build the docker image from the project home with `docker build -t teamspeak_web:latest -f docker/Dockerfile .`
* Re-create app container with latest image

###### docker-compose

* Change directory to project home
* `git pull`
* Build the docker image from the project home with `docker build -t teamspeak_web:latest -f docker/Dockerfile .`
* Change directory to `docker-compose/` folder
* Execute `docker-compose down`
* Execute `docker-compose up -d`

### Manual installation and deployment

#### Install

* Clone repository
* Install composer
* Change directory to project home
* Execute `composer install`
* `composer install`

#### Web server
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
* Everything should be up and running. Point your browser to `8080` to see the web interface.

#### Upgrade

* Change directory to project home
* `git pull`
* `composer update`

### Contributions & Development
* Follow manual install guide but use the internal server with `php -S localhost:8080 -t public public/index.php`
* Point browser to [localhost:8080](http://localhost:8080)

#### Helpers

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

#### Translations
- This app uses Symfony Translator. It's bootstrapped in `Util\BootstrapHelper` and locales are placed under `data/locale/`. Adjust to your needs or help translating.
- Form fields (name/id should be the same) are also translated. For a field named `content` or `ConT enT` translate `form_field_content`.

#### Theme
Themes can be chosen in the `env` file by editing the `theme` variable. Templates are mapped to the corresponding view folder in `src/View/<themeName>`. `.css`, `.js` and other style files like `.ttf` or `.woff2` for fonts should be placed in `public/theme/<themeName>` and accessed accordingly. See an example in `src/View/boostrap4/layout.twig`.
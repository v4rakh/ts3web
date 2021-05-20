# README

ts3web is a free and open-source web interface for TeamSpeak 3 instances.

The minimalistic approach of this application is intentional.

* Docker images available on [https://hub.docker.com/r/varakh/ts3web](https://hub.docker.com/r/varakh/ts3web)
* Sources are hosted on [https://github.com/v4rakh/ts3web](https://github.com/v4rakh/ts3web)

There are many TeamSpeak 3 web interfaces out. Why should I pick ts3web? Free, simple, stateless, easy to extend,
standard bootstrap theme.

## F.A.Q

Questions? Here you'll hopefully get the answer. Feel free to read before starting.

<a name="flood"></a>

###### I always get `flood client` message when clicking anywhere in the web interface.

ts3web makes heavy use of query commands. When your instance is up and running, you should be able to change
`serverinstance_serverquery_flood_commands` to a high value, e.g. `100` and `serverinstance_serverquery_flood_time` to
`1` which is enough.

<a name="whitelist"></a>

###### I always get `TSException: Error: host isn't a ts3 instance!` when selecting a server.

You probably got query banned from your server. You need to properly define
your [`whitelist.txt` file](#whitelisttxtexample)
and include it in your TeamSpeak application.

<a name="dockerperms"></a>

###### I always get `no write permissions` or something similar when trying to save snapshots or when a log entry is created.

This probably happens when you're in the docker setup. Ensure that host binds have permissions set up properly. The user
which is used in the docker container is `nobody` with id `65534`. If, e.g. logs are host bound, then execute
`chown -R 65534:65534 host/path/to/log`. The same holds true for snapshots.

## Configuration

The main configuration file for the *web interface* is the `env` file located in `config/`. There's an example file
called `env.example` which you **need** to copy to `config/env`. Defaults will assume you're running your TeamSpeak
server on `localhost` with default port. Docker deployments can and *should* host bind this file into the container
directly and just maintain the `env` file.

## Deployment

The application can be deployed two different ways. See below for more information. For each deployment type a running
TeamSpeak 3 instance is a prerequisite.

In the `docker-compose.yml` [example](#dockercompose), a setup together with a teamspeak server instance is shown.

### Docker

#### Important. Read before setup!

1. [Setup write permissions if you're using host binds](#dockerperms)
2. [Ensure that you set `flood commands` to a higher value in your TeamSpeak](#flood).
3. [Use a `whitelist.txt` to ensure the web interface will not be query banned](#whitelist)
4. Be aware that the web interface will not be able to use `localhost` as TeamSpeak 3 server address because it's not
   available in a docker container. The public address also has to match the environment variable
   `teamspeak_host=your-public-address` within the `env` file.

<a name="dockerrun"></a>

#### docker run

The following section outlines a manual setup. Feel free to use the provided `docker-compose.yml` as quick setup.

1. Create docker volumes for `snapshots`, `log` and `env`. Alternative is to host bind them into your containers.
2. Create a docker network with a fixed IP range or later use host network.
3. Depending on your setup, you need to change `teamspeak_host` of your `env` file to point either to `your IP` or to a
   `fixed docker IP` which your teamspeak uses. `localhost` is not valid if you're using it in docker. If you're unsure,
   please take a look at the example `docker-compose.yml` files.
4. Start a container using the docker image `varakh/ts3web` and provide the following bindings for volumes:
    * `{env_file_volume|host_file}:/var/www/html/applicationconfig/env`
    * `{snapshot_volume|host_folder}:/var/www/html/application/data/snapshots`
    * `{log_volume|host_folder}:/var/www/html/application/log`
5. [Ensure that you're whitelisting the IP from which the webinterface will issue commands.](#whitelist)
6. Run the `docker run` command including your settings, volumes and networks (if
   any): `docker run --name teamspeak_web -v ./env:/var/www/html/application/config/env -p 8181:80 varakh/ts3web:latest`
   .

<a name="dockercompose"></a>

#### docker-compose

In order for TeamSpeak to show correct IP and country flags, the `network_mode = "host"` is advised. It's also possible
to set everything up [without using the host network mode and use fixed IPs](#withouthostmode).

The examples will use host binds for volumes. Feel free to adapt the `docker-compose.yml` template and use docker
volumes instead if you like.

Ensure to [apply permissions](#dockerperms) for volumes though.

<a name="withhostmode"></a>

#### With 'host' mode

```
version: '2.1'
networks:
  teamspeak:
    external: false
services:
  app:
    container_name: teamspeak_app
    image: teamspeak:latest
    volumes:
      - ./ts3server:/var/ts3server
      - ./whitelist.txt:/whitelist.txt
    ports:
      - 10011:10011
      - 30033:30033
      - 9987:9987/udp
    environment:
      - TS3SERVER_LICENSE=accept
      - TS3SERVER_IP_WHITELIST=/whitelist.txt
    restart: always
    network_mode: "host"
  web:
    container_name: teamspeak_web
    image: varakh/ts3web:latest
    volumes:
      - ./env:/var/www/html/application/config/env
      - ./snapshots:/var/www/html/application/data/snapshots
      - ./log:/var/www/html/application/log
    ports:
      - 127.0.0.1:8181:80
    depends_on:
      - app
    restart: always
    networks:
      - teamspeak
```

<a name="withouthostmode"></a>

#### Without 'host' mode

```
version: '2.1'
networks:
  teamspeak:
    driver: bridge
    ipam:
     config:
       - subnet: 10.5.0.0/16
         gateway: 10.5.0.1

services:
  app:
    container_name: teamspeak_app
    image: teamspeak:latest
    volumes:
      - ./ts3server:/var/ts3server
      - ./whitelist.txt:/whitelist.txt
    environment:
      - TS3SERVER_LICENSE=accept
      - TS3SERVER_IP_WHITELIST=/whitelist.txt
    restart: always
    ports:
     - 10011:10011
     - 30033:30033
     - 9987:9987/udp
    networks:
      teamspeak:
        ipv4_address: 10.5.0.5
  web:
    container_name: teamspeak_web
    image: varakh/ts3web:latest
    volumes:
      - ./env:/var/www/html/application/config/env
      - ./snapshots:/var/www/html/application/data/snapshots
      - ./log:/var/www/html/application/log
    ports:
      - 127.0.0.1:8181:80
    depends_on:
      - app
    restart: always
    networks:
      teamspeak:
        ipv4_address: 10.5.0.6

```

<a name="whitelisttxtexample"></a>

#### whitelist.txt

The following illustrates a valid `whitelist.txt` file which can be used for the above `docker-compose` setups. You need
to replace `your-public-ip` with the TeamSpeak's public IP address if required or remove the fixed internal docker IP if
you're on 'host' mode.

```
127.0.0.1
::1
10.5.0.5
your-public-ip
```

Now execute `docker-compose up -d` to start those containers. If you like to update, do `docker-compose down`,
`docker-compose pull` and then `docker-compose up -d` again.

Your TeamSpeak 3 Server will be available under `public-server-ip:9987`. The web interface will be available on
`127.0.0.1:8181`. You need to add a [reverse proxy](#reverseproxy) and probably you also want SSL configured if you
expose it via domain. For testing purposes, change `- 127.0.0.1:8181:80` to `- 8181:80`. The web interface will then be
available under
`public-server-ip:8181`.

This is **not recommended**! Secure your setup properly via [reverse proxy and SSL](#reverseproxy).

### As native PHP application

**Prerequisite**: `php`, `composer` and probably `php-fpm` installed on the server.

#### Install:

* Clone repository
* Change directory to project home
* Execute `composer install`
* `composer install`
* Do the configuration by coping the `env.example` file (see information above)
* Use a web server _or_ run directly via the embedded PHP server: `php -S localhost:8080 -t public public/index.php`.
* Point your browser to [localhost:8080](http://localhost:8080)
* Apply any [whitelist.txt](#whitelisttxtexample) changes if you configured `teamspeak_host` differently
  than `localhost`

#### Upgrade:

* Change directory to project home
* `git pull`
* `composer update`

<a name="reverseproxy"></a>

### Reverse proxy

Here's an example on how to configure a reverse proxy for the web interface docker container

  ```  
  root   .../public;
  index index.php;   
  
  # enable and setup if you have a certificate (highly recommended)
  #ssl on;
  #ssl_certificate fullchain.pem;
  #ssl_certificate_key privkey.pem; 
  
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

## Limitations

TeamSpeak has a detailed interface for permissions and uploading files, therefore the following features are not
supported:

* uploading files (only viewing and deleting, use the official client for uploading)
* editing permissions (only viewing, use the client for editing)

## Development

If you're willing to contribute, here's some information.

### Release

* Set a date in the `CHANGELOG.md` file
* Remove `SNAPSHOT` from the version in `Constants.php`
* Build the docker image from the project
    * if necessary, add GitHub access token to let composer pull dependencies within the image correctly:
      add `&& composer config --global --auth github-oauth.github.com <token> \` before the `composer install` command,
      where `<token>` can be retrieved from [GitHub settings](https://github.com/settings/tokens)
    * execute `sudo docker build --no-cache -t varakh/ts3web:latest .` to build
    * publish it
* Tag the release git commit and create a new release in the VCS web interface

### Prepare next development cycle

1. Branch from `master` to `release/prepare-newVersionNumber`
2. Add `-SNAPSHOT` to the version in `Constants.php` and increase it
3. Merge this branch to `patch` or/and `dev` respectively
4. Don't forget to clean up all created branches

### Helpers

Attributes can be defined when including `table`, `keyvalues` and `form` templates of twig. This helps to generate
tables and forms without the need to specify all attributes.

```
hiddenDependingOnAttribute // hides a row depending on a value in a table
hiddenColumns // hides an entire column depending on a key in a table
links // generates a link for a specific cell in a table or keyvalue
additional_links // generates extra columns in a table
filters // applies filters depending on a key in a table or key value view
attributesEditable // define editable attributes in the key value view
fields // define fields for a form
```

See example usage in the folder `View/bootstrap4`.

### Translations

- This app uses Symfony Translator. It's bootstrapped in `Util\BootstrapHelper` and locales are placed
  under `data/locale/` and the data table `.json` file, e.g. `en_dataTable.json`. Adjust to your needs or help
  translating.
- Form fields (name/id should be the same) are also translated. For a field named `content` or `ConT enT`
  translate `form_field_content`.

### Theme

Themes can be chosen in the `env` file by editing the `theme` variable. Templates are mapped to the corresponding view
folder in `src/View/<themeName>`. `.css`, `.js` and other style files like `.ttf` or `.woff2` for fonts should be placed
in `public/theme/<themeName>` and accessed accordingly. See an example in `src/View/boostrap4/layout.twig`.

# README
ts3web is a free and open-source web interface for TeamSpeak 3 instances.
 
The minimalistic approach of this application is intentional.

* Docker images available on [https://hub.docker.com/r/varakh/ts3web](https://hub.docker.com/r/varakh/ts3web)
* Sources are hosted on [https://github.com/v4rakh/ts3web](https://github.com/v4rakh/ts3web)

## Limitations
Features which are currently not supported:

* upload files (only viewing and deleting)
* modify permissions (only viewing)

## F.A.Q

###### There are lots of TeamSpeak 3 web interfaces out. Why should I pick ts3web? 
Free, simple, stateless, easy to extend, standard bootstrap theme.

###### I always get `TSException: Error: host isn't a ts3 instance!` when selecting a server.
You probably got query banned from your server. You need to properly define your `whitelist.txt` file and include it in 
your TeamSpeak application.

## Configuration
The main configuration file is the `env` file located in `config/`. There's an example file called `env.example` 
which you **need** to copy to `config/env`. Defaults will assume you're running your TeamSpeak server on `localhost` with 
default port. Docker deployments can and *should* host bind this file into the container directly and just maintain the 
`env` file.

## Deployment
The application can be deployed in different ways. See below for more information. For each deployment type a running 
TeamSpeak 3 instance is a prerequisite (except for the `docker-compose.yml` type which will start also the server if 
needed).

### Exposed volumes on docker images
* Snapshots are saved in `/var/www/html/application/data/snapshots`. You should create a volume for this location if 
you're using docker as deployment type.
* Logs are saved in `/var/www/html/application/log` for docker containers. You should create a volume 
for this location if you're using docker as deployment type. You should also create the log file `application.log` already.

**Important**: Ensure that host binds have permissions set up properly. The user which is used in the docker container 
is `www-data` with id `82`. If, e.g. logs are host bound, then execute `chown -R 82:82 host/path/to/log`. 
The same holds true for snapshots.

### Usage with docker-compose
The recommended way is to use docker-compose. The `network_mode = "host"` is required in order to show correct IP 
addresses of connected TeamSpeak users.

1. The web interface will not be able to use `localhost` as TeamSpeak 3 server address because it's not available in a 
docker container when _not_ using the `host` network. Thus the`whitelist.txt` **must** include your public TeamSpeak 3 
server IP for this example setup.
2. The public address also has to match the environment variable `teamspeak_host=your-public-address` within
the `env` file referenced in the example `docker-compose`.

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
      - ./app:/var/ts3server
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
    image: teamspeak_web:latest
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
    healthcheck:
        test: "nc -z localhost 80"
        interval: 1s
        timeout: 10s
        retries: 5
```

Now execute `docker-compose up -d` to start those containers. If you like to update, do `docker-compose down`, 
`docker-compose pull` and then `docker-compose up -d` again.

Your TeamSpeak 3 Server will be available under `public-server-ip:9987`. The web interface will be available on
`127.0.0.1:8181`. You need to add a reverse proxy and probably you also want SSL configured if you expose it via domain.
For testing purposes, change `- 127.0.0.1:8181:80` to `- 8181:80`. The web interface will then be available under 
`public-server-ip:8181`. This is **not recommended**! Secure your setup properly via reverse proxy and SSL!

### Usage as single docker container
* Copy `env.example` to `env` and adjust to your needs. It's recommended to make it persistent outside of the container.
* Create a container with the image, e.g. `docker run --name teamspeak_web -v ./env:/var/www/html/application/config/env -p 8181:80 varakh/ts3web:latest`. 
* Make sure that if TeamSpeak and ts3web share the same docker instance and that they should be put into one network and the subnet **needs be added to teamspeak's query whitelist**.
* Point your browser to `8181` to see the web interface. 

### Usage as native application
**Prerequisite**: `php`, `composer` and probably `php-fpm` installed on the server.

To install:
* Clone repository
* Change directory to project home
* Execute `composer install`
* `composer install`
* Do the configuration by coping the `env.example` file (see information above)
* Use a web server or run directly via PHP server: `php -S localhost:8080 -t public public/index.php` (point browser to [localhost:8080](http://localhost:8080))

To upgrade:
* Change directory to project home
* `git pull`
* `composer update`

### Web server setup
* Example `nginx.conf` for **standalone** deployment without SSL:

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

* Example `nginx.conf` as **reverse proxy** with SSL:
    
    ```
    server {
        listen      443 ssl http2;
        server_name teamspeak.domain.tld;
      
        ssl on;
        ssl_certificate fullchain.pem;
        ssl_certificate_key privkey.pem;
    
        location / {
            proxy_pass http://localhost:8181;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header Host $host;
            proxy_set_header X-Forwarded-For $remote_addr;
        }
    }
    ```

## Development

### Release
* Set a date in the `CHANGELOG.md` file
* Remove `SNAPSHOT` from the version in `Constants.php`
* Build the docker image from the project home with `docker build -t varakh/ts3web:latest -t varakh/ts3web:<releaseTag> -f docker/Dockerfile .` and publish it
* Tag the release git commit and create a new release in the VCS web interface

### Prepare next development cycle

1. Branch from `master` to `release/prepare-newVersionNumber`
2. Add `-SNAPSHOT` to the version in `Constants.php` and increase it
3. Merge this branch to `patch` or/and `dev` respectively
4. Don't forget to clean up all created branches

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

See example usage in the folder `View/bootstrap4`.

### Translations
- This app uses Symfony Translator. It's bootstrapped in `Util\BootstrapHelper` and locales are placed under `data/locale/` and the data table `.json` file, e.g. `en_dataTable.json`. Adjust to your needs or help translating.
- Form fields (name/id should be the same) are also translated. For a field named `content` or `ConT enT` translate `form_field_content`.

### Theme
Themes can be chosen in the `env` file by editing the `theme` variable. Templates are mapped to the corresponding view 
folder in `src/View/<themeName>`. `.css`, `.js` and other style files like `.ttf` or `.woff2` for fonts should be placed 
in `public/theme/<themeName>` and accessed accordingly. See an example in `src/View/boostrap4/layout.twig`.

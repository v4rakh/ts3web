# README

ts3web is a web interface for one TeamSpeak 3 Server. It's using serverquery to login. Sources can be viewed
[here](https://git.myservermanager.com/alexander.schaeferdiek/ts3web).
 
This web interface aims to be as simple as possible. The minimalistic approach is intentional.

Feel free to submit pull requests if you like to help. More information are here: [https://hub.docker.com/r/varakh/ts3web](https://hub.docker.com/r/varakh/ts3web)

Features which are currently **not supported**:

* modify permissions (only viewing)
* modify files (only viewing)

**ts3web** can be deployed in different ways. See below for more information. For each deployment type a running 
TeamSpeak 3 server is a prerequisite (except for the `docker-compose.yml` type which will start also the server if 
needed).

## Configuration

The main configuration file is the `env` file located in `config/`. There's an example file called `env.example` 
which you can copy to `config/env`. Defaults will assume you're running your TeamSpeak server on `localhost` with 
default port. Docker deployments can host bind this file into the container directly and just maintain the `env` file.

## Usage with docker-compose

The recommended way is to use docker-compose. The `network_mode = "host"` is required in order to show correct IP 
addresses of connected users.

1. The web interface will not be able to use `localhost` as TeamSpeak 3 server address because it's not available in a 
docker container not using the `host` network. Thus the`whitelist.txt` **must** include your public TeamSpeak 3 server 
IP for this example setup.
2. The public address also has to match the environment variable `teamspeak_host=your-public-address` within
the `env` file referenced in the example `docker-compose`.

```
version: '2'
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
    ports:
      - 127.0.0.1:8181:80
    depends_on:
      - app
    restart: always
    networks:
      - teamspeak
```

Now execute `docker-compose up -d` to start those containers. If you like to update, do `docker-compose down`, 
`docker-compose pull` and then `docker-compose up -d` again.

Your TeamSpeak 3 Server will be available under `public-server-ip:9987`. The web interface will be available on
`127.0.0.1:8181`. You need to add a reverse proxy and probably you also want SSL configured if you expose it via domain.
For testing purposes, change `- 127.0.0.1:8181:80` to `- 8181:80`. The web interface will then be available under 
`public-server-ip:8181`. This is **not recommended**! Secure your setup properly via reverse proxy and SSL.

## Usage as single docker container

* Copy `env.example` to `env` and adjust to your needs. It's recommended to make it persistent outside of the container.
* Create a container with the image, e.g. `docker run --name teamspeak_web -v ./env:/var/www/html/application/config/env -p 8181:80 varakh/ts3web:latest`. 
* Make sure that if teamspeak and ts3web share the same docker instance they should be put into one network and the subnet **needs be added to teamspeak's query whitelist**.
* Point your browser to `8181` to see the web interface. 

## Usage as native application
**Prerequisite**: `php`, `composer` and probably `php-fpm` installed on the server.

To install:
* Clone repository
* Change directory to project home
* Execute `composer install`
* `composer install`
* Use a web server or run directly via PHP server: `php -S localhost:8080 -t public public/index.php` (point browser to [localhost:8080](http://localhost:8080))

To upgrade:
* Change directory to project home
* `git pull`
* `composer update`

## Web server setup
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
* Build the docker image from the project home with `docker build -t varakh/ts3web:latest -f docker/Dockerfile .` and publish it
* Tag the release git commit and create a new release in the VCS web interface 

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
- This app uses Symfony Translator. It's bootstrapped in `Util\BootstrapHelper` and locales are placed under `data/locale/`. Adjust to your needs or help translating.
- Form fields (name/id should be the same) are also translated. For a field named `content` or `ConT enT` translate `form_field_content`.

### Theme
Themes can be chosen in the `env` file by editing the `theme` variable. Templates are mapped to the corresponding view 
folder in `src/View/<themeName>`. `.css`, `.js` and other style files like `.ttf` or `.woff2` for fonts should be placed 
in `public/theme/<themeName>` and accessed accordingly. See an example in `src/View/boostrap4/layout.twig`.
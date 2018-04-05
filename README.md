# README #

**ts3web** is a webinterface for any TeamSpeak 3 Server used with serverQuery login.
 
Most TeamSpeak 3 interfaces are bloated although nearly all configuration can be done entirely in the TeamSpeak 3 Client.  
 
This webinterface aims to be as simple as possible. It does not provide complex features which can be configured within the client. Instead, it only supports features considered useful for a TeamSpeak 3 web interface.  **The minimalistic approach is intentional!** 

If you like to help (to translate or implement missing features), open an issue before. Then create a pull request. You should use existing code to implement new features. PRs will be merged after a code review.

Things you **cannot** do:
- Server Groups create, edit
- Channel Groups create, edit
- Channels create
- Permissions add, edit, delete (server, channel, client)
- File management (create, download, delete)
- features which are not *explicitly* supported

Things you **can** do:
- view
    - instance and host information
    - global log
    - virtual servers
    - users online
    - all known clients
    - channels
    - groups
    - channel groups
    - files
    - banlist
    - complain list
    - permissions (server, channel, client)
- edit
    - virtual server
    - instance
- delete
    - bans
    - complains
    - virtual servers
    - clients
    - server groups
    - channel groups
- other actions
    - create virtual servers
    - generate serverQuery password
    - send message to users, servers, channels
    - ban a user
    - kick a user
    - poke a user
    - add to server group
    - remove from server group

## Install ##

* Clone repository
* Install composer
* Change directory to project home
* Copy `config/env.example` to `config/env` and adjust to your needs
* `composer install`

## Deployment ##
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
    
## Upgrade ##

* Change directory to project home
* `git pull`
* `composer update`

## Developers ##
* start server with `php -S localhost:8080 -t public public/index.php`
* point browser to [localhost:8080](http://localhost:8080) to have a preview

## Translations ##
- This app uses Symfony Translator. It's bootstrapped in `Util\BootstrapHelper` and locales are placed under `data/locale/`. Adjust to your needs or help translating.
- Form fields (name/id should be the same) are also translated. For a field named `content` or `ConT enT` translate `form_field_content`.


## Theme ##
Themes can be chosen in the `env` file by editing the `theme` variable. Templates are mapped to the corresponding view folder in `src/View/<themeName>`. `.css`, `.js` and other style files like `.ttf` or `.woff2` for fonts should be placed in `public/theme/<themeName>` and accessed accordingly. See an example in `src/View/material/layout/header.twig`.
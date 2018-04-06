# README #

**ts3web** is a webinterface for any TeamSpeak 3 Server used with serverQuery login.
 
This webinterface aims to be as simple as possible. It does not provide complex features. Instead, it only supports features considered useful for a TeamSpeak 3 web interface.  **The minimalistic approach is intentional!** 

If you like to help (to translate or implement missing features), open an issue first. You should use existing code to implement new features. PRs will be merged after a code review.

Things you **cannot** do:
- Permissions Management (add, edit, delete for servergroups, channelgroups, clients)
- File Management (except viewing)
- Temporary passwords
- Move online clients

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

### Helpers ###

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

## Translations ##
- This app uses Symfony Translator. It's bootstrapped in `Util\BootstrapHelper` and locales are placed under `data/locale/`. Adjust to your needs or help translating.
- Form fields (name/id should be the same) are also translated. For a field named `content` or `ConT enT` translate `form_field_content`.


## Theme ##
Themes can be chosen in the `env` file by editing the `theme` variable. Templates are mapped to the corresponding view folder in `src/View/<themeName>`. `.css`, `.js` and other style files like `.ttf` or `.woff2` for fonts should be placed in `public/theme/<themeName>` and accessed accordingly. See an example in `src/View/material/layout/header.twig`.
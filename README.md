# Phalcon Rocket App 1.3.1-beta
***
####A PhalconPHP base app
Phalcon Rocket App provides PhalconPHP developers with a structured foundation, common features and 
useful functions to kick start your project. It's a base application that's ready for you to turn into 
any kind of application you want. Phalcon Rocket App is based on Mruz's base app which he no longer seems 
to be maintaining. I have made many changes, improvements and additions so that it better serves my 
application needs, so I make it available here for anyone else who may find it useful.

####[Try The Demo](http://phalcon-rocket.phuct.org/)

###What is it and what does it do?
Phalcon Rocket App is not a complete application. It provides the structure and a number of components that 
are commonly required by most web applications, so that your app development can start at a more advanced 
stage, with out you having to bootstrap and build common components from scratch. Common components include 
things like role based member auth, sign up, login and contact forms and so on.

Phalcon Rocket App is a multi modular (HMVC) app. It uses Semantic UI which I personally think is a lot 
better than Twitter Bootstrap.


###Feature Overview
* Fully structured HMVC base app for PhalconPHP
* Semantic UI
* MySQL schema
* PHPMailer
* Role based user auth
    * Brute force attack protection on login form (configurable)
    * Password strength validation (configurable)
    * User password reset
    * Resend activation email
    * User can change password & email address
* I18n language translation
* Volt templating & markdown support - yep it will read and render a view saved as `.md` and written in markdown
* Recaptcha integration on forms (configurable)
* Multiple environment
    * development - display debug, always compile template files, always minify assets
    * testing - log debug, only checks for changes in the children templates, checks for changes and minify assets
    * staging - log debug, notify admin, only checks for changes in the children templates, checks for changes and minify assets
    * production - log debug, notify admin, don't check for differences, don't create missing files, compiled and minified files must exist before!
* CLI & console file
* config.ini files for mutiple environments
* Contact form
* Frontend, backend (admin), CLI & documentation default modules
* Various useful classes and extensions

###Requirements
* PHP >= 5.4
* PhalconPHP 2.x
* MySQL

##Installation
1. Download or clone into the document root of your development server.
2. Create a MySQL database and import the `.sql` file.
3. Go to `/app/common/config/` and edit the config.ini, development.ini and production.ini files
* IMPORTANT: `hash_key` & `[crypt] key` should have their values replaced with your own secret keys
* Recaptcha is disabled by default in development. You can enable it from within `development.ini`
 
##Changes since Base App 2
Here are some of the things that have changed since Mruz's Base App 2

* Updated to work with Phalcon 2.x
* User auth system completely revamped, secured and expanded
* Payment (buy) system removed as it is not a common component
* Now uses Semantic UI instead of Twitter Bootstrap
* Recaptcha integration added
* Environment specific config files added
* New config settings added
* Various extensions and classes added
* App directory is now portable so it is easy to move it to outside your public html directory
* And much more
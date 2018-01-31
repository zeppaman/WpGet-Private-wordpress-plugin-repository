# Private Wordpress plugin repository
Wordpress private repository for plugins.
[![CircleCI](https://circleci.com/gh/zeppaman/WpGet.svg?style=svg)](https://circleci.com/gh/zeppaman/WpGet)

**Provide updates for plugins**
WpGet does that, keeping all packages and providing to the wordpress installation. Using token based authentication all process are kept secure.

**Easy to setup**
Setup is easy. Just a copy of the bundle to the server and few steps more. Plugin integration is very easy, just a class to add.

**Api oriented**
All core feature are exposed by API. This will allow you to integrate the delivery process with other tools in the company.

**Ui to get control**
A simple ui show what package are loaded and give you the power.

**Low Server Requirements**
Just a webserver with php and a database. For few data, using SQLite, neither database.

## Why we need a private repository

Wordpress is the most used CMS in the world with the most important plugin system. It's easy for a developer to create and publish a new plugin into the public reposiory. But what if i had to create and mantain a private plugin? Im speaking about:

* a plugin with an high intellectual content,
* a plugin not ready for the public, maybe focused on some particular business case
* a custom super-plugin built used to inject custom plugin dependency

There are actually these opportunities into public repository:

1. Make a free plugin
2. Make a paied plugin (but placing into main repository it is available to everyone open to buy it)
3. Intall you plugin manually

Firts two option are the most widely adopted by public plugin sellers, while the third one is a very common practice into agency or companies that have some common plugin reused in many websites.

*WpGet* came to give a forth opportuninty. It is conceived as a private repository where you can push plugins and allow wordpress installation to update from that source, in addiction to the standard platform. For who comes from other technlogy *WpGet is similar to NuGet, Maven or NPM package manager*. 

Here a siple diagram to explain how WpGet works.

<image here>
  
 ## How to upload a package
 It can be done using curl, wget, php or any other tool able to send an HttpRequest in multipart/form POST mode.
 
*CURL EXAMPLE*
```bash
curl  -H "Authorization: Bearer <ACCESS_TOKEN>" \
  -F "name=package" \
  -F "version=1.1.1" \
  -F "reposlug=default" \
  -F "file=@/C/temp/wgettest/package_v1.0.0.zip" \
  http://localhost:3000/web/catalog/Package
```


*POWERSHELL EXAMPLE*
```powershell
Invoke-RestMethod  -Header @{"Authorization" = "Bearer <ACCESS_TOKEN>"} -Method Post -InFile $filePath -ContentType "multipart/form-data" -Uri http(s)://www.your-site.wpget/ui/catalog/Package?name=<packageName>&version=X.Y.Z
```

Any other needs or suggestion? [Please open a issue](https://github.com/zeppaman/WpGet/issues/new)



## How to implement a WpGet compliant plugin
This is easy. We have prepared a ready-to-use php updater class that interact with plugin repository to keep plugin updated. This needs only to setup some wordpress settings (repository url and access token). After that your plugin will check for newer version using WpGet and will download them using the private repository. Easy!

For now, [just copy and paste this class into your plugin](https://raw.githubusercontent.com/zeppaman/WpGet/master/src-client/WpGetUpdater.php). In next future we are going to deliver this using a composer package and a public wordpress plugin.

```yaml
version: 1.2.4 # versione in form major.minor.build
name: name of the package
description: >
 Long description that can contains also html statements
 <h1> but remember to indent all lines with spaces to be YAML compliant! </h1>
changelog: Write here changelogs. This also can be a multiline field...
```
 
## How to install the server
 
### Requirements
 
 This application requieres PHP 7+ and a database. Any eloquent database is supported, but atm WpGet is tested only over MySQL platfrorm that's also the development one. About webserver, no limitation. Apache or Nginx are equivlent, but must be configured to serve php requests. Php modules needed are just *mbstring* and the *pdo_xxx* where xxx is the db driver according with configuration. 
 
| Topic  | Version |
| ------------- | ------------- |
| PHP | 7+  |
| DB  | MySQL or other eloquent supported database  |
| ext  | mbstring, pdo_xxx  |
| Web  | apache, nginx,...  |

**Note for apache users:** You may have problem with authentication headers. In case, [Please check this resource](https://github.com/slimphp/Slim/issues/831)

### Install 
**A. Setup the server**
Starting from released bundle:
1. Download and copy budle somewhere in server
2. Configure web server to server /web folder. Yes, You do not have to expose the full package, but only this folder.
3. There isn't any url rewriting needs, so in most cases that's all on server side
4. Edit /config/settings.php updating connection string. [More info here.](https://github.com/zeppaman/WpGet/wiki/configure-wpget-wordpress-repository)

**B. Configure**
1. Enter http(s)://www.your-site.wpget/ui/
2. First access will install database structure.
3. First time you can login using admin\admin credential
4. Change password under user managment

* Now you are ready to start using WpGet *

 


## Wiki reference

Usage

* [deploy a package](deploy-wget-package)

Dev: extend and contribute
* [Setup local env](https://github.com/zeppaman/WpGet/wiki/wpget-setup-local-env)
* [API](https://github.com/zeppaman/WpGet/wiki/wget-api-private)


 ## License

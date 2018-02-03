#install node
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar self-update
sudo mv composer.phar /usr/local/bin/composer
composer install -n --prefer-dist
curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
sudo apt-get install -y nodejs

#angular build
cd src-ui;
npm install;
npm run ng build --env prod;
cd ..;

#delete unused folders
rm -rf src-ui;
rm -rf .circleci;
rm -rf .git;
rm -rf .vscode; 

#build artifact
mkdir /tmp/artifacts;
zip -r /tmp/artifacts/web.zip .

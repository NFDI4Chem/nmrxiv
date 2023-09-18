# Centos

nmrXiv project is built with [Laravel](https://laravel.com/docs/8.x) web application framework which comes with [Sail](https://laravel.com/docs/8.x/sail), a built-in solution for running your Laravel project using Docker. 
The whole project is a package of below services and features.
* [PostgreSQL](https://www.postgresql.org/docs/)
* [Redis](https://redis.io/docs/)
* [Selenium](https://www.selenium.dev/documentation/)
* [Meilisearch](https://docs.meilisearch.com/)
* [MailHog](https://mailtrap.io/blog/mailhog-explained/)

### Dependencies
* [git]
* [PHP 8.0 or more]
* [Composer]
* [Docker Engine]
* [Docker Compose]

### Setup:

Install GIT
```
$ sudo dnf update -y
$ sudo dnf install git -y
$ git --version
```

Install docker
```
$  sudo yum remove docker \
                  docker-client \
                  docker-client-latest \
                  docker-common \
                  docker-latest \
                  docker-latest-logrotate \
                  docker-logrotate \
                  docker-engine

# Install using the repository
$ sudo yum install -y yum-utils
$ sudo yum-config-manager \
    --add-repo \
    https://download.docker.com/linux/centos/docker-ce.repo

$ sudo yum install docker-ce docker-ce-cli containerd.io

# If podman / buildah packages conflict with the docker installation. You need to remove those packages using the following command and reinstall docker again

$ sudo yum erase podman buildah
$ sudo systemctl start docker
$ sudo docker run hello-world
```

Install docker compose

```
$ curl -L "https://github.com/docker/compose/releases/download/1.27.4/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
$ chmod +x /usr/local/bin/docker-compose
```

Now that we have installed docker and docker-compose. Let's install PHP >8.0 and composer to get started with Laravel sail

Install PHP 8.0

```
$ sudo dnf -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm
$ sudo dnf -y install https://rpms.remirepo.net/enterprise/remi-release-8.rpm
$ sudo dnf -y install yum-utils
$ sudo dnf module reset php
$ sudo dnf module install php:remi-8.0 -y
$ sudo dnf install php -y
$ sudo dnf -y install php-{cli,fpm,mysqlnd,zip,devel,gd,mbstring,curl,xml,pear,bcmath,json}
```

Install composer

Please replace the 'hash' in the command below from the composer site [here](https://getcomposer.org/download/)

```
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === '<hash>') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
```

Now with composer installed lets go ahead and install all the php dependencies. But before we can do that we need to clone the project locally.

```bash
git clone https://github.com/NFDI4Chem/nmrxiv.git
```
* Go to the project directory:
```bash
cd nmrxiv
```
* Navigate to the development branch:
```bash
git checkout development
```
* Create an `.env` file, and copy the existing `.env.example` into it:
```bash
cp .env.example .env 
```
* Install the dependencies from the `composer.lock` file:
```bash
composer install
```
* Start [Sail](https://laravel.com/docs/8.x/sail#starting-and-stopping-sail) to build application containers on your machine:
```bash
./vendor/bin/sail up
```

If your sail could not find the docker you need to open up the docker binary permissions using the following command

```
$ sudo chmod 666 /var/run/docker.sock
```

If your docker is failing to download os packages you need to update your firewall settings

In your config file 

``` 
$ sudo nano /etc/firewalld/firewalld.conf
```

please change

```
FirewallBackend=nftables
```

to 
```
FirewallBackend=iptables
```

Save this change and reload firewall

```
$ sudo systemctl restart firewalld.service
```

Once the application's Docker containers have been started, you can access the application in your web browser at [http://localhost](http://localhost). 

If you find the error message indicating the app key doesn't exist. You will be prompted to <b>Generate app key</b> (you will find the button on the error page below the error message). After pressing the generation button, the following message is shown on the screen: "The solution was executed successfully. Refresh now." After refreshing, you access the application.

At this stage, you haven't run the application migrations (no database tables for the application to connect to). Again you will be prompted with an error message. You will also find a button just below the message to run migrations. Please click it and you are good to go.

For local development, you will need nodejs and npm to install and compile node packages

```
$ sudo dnf install -y gcc-c++ make 
$ curl -sL https://rpm.nodesource.com/setup_16.x | sudo -E bash - 
```

Install all the npm packages
```
$ npm install
```

Compile Javascript code
```
$ npm run build
```

In order to actively watch and compile changes
```
$ npm run watch
```
:::caution Recommendation
Follow our [code contribution guidelines](/docs/developer-guides/code-contribution-guidelines) to make a pull request.
:::

:::info Info
Click to learn more about [Laravel](https://laravel.com/docs/9.x) and [installation guides](https://laravel.com/docs/9.x/installation).
:::
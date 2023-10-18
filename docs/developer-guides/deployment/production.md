# Production

### Introduction

When deploying the nmrXiv application to production, there are some needed steps to make sure our application is running as efficiently as possible. In this document, we will cover some basic configuration required for the same in production environment.

### Server Configuration

#### Nginx

If deploying the application in `nginx` the following configuration file can be referred as a starting point for configuring the web server.
It should be ensured, like the configuration below, that the web server directs all requests to the application's public/index.php file. The index.php file should not be moved to the project's root, as serving the application from the project root will expose many sensitive configuration files to the public Internet.

```bash
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name <server-name>;
    server_tokens off;

    root /var/www/html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    
    charset utf-8;
    location / {
        try_files $uri $uri/ /public/index.php?$query_string;
    }
    
    index /public/index.html /public/index.htm /public/index.php;
    error_page 404 /public/index.php;

    location ~ \.php$ {
        fastcgi_pass localhost:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }
    
    location ~* \.(png|jpg|jpeg|gif|svg|ico|woff2|woff)$ {
        expires 7d;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Optimization

When deploying the application to production, the below command should be considered and run during your deployment process for better optimization and performance.

- <b>Autoloader Optimization</b> -

Below command will optimize Composer's class autoloader map.

```bash
composer install --optimize-autoloader --no-dev
```

:::info Info
Info: In addition to above, the composer.lock file should be present in the project's source control repository, which will make the project's dependencies installation much faster.
:::

- <b>Optimizing Configuration Loading</b> -

To greatly reduce the number of trips the framework would make to filesystem when loading the configuration files, below command should be executed to combine all the configuration files into a single, cached file.

```bash
php artisan config:cache
```

- <b>Optimizing Route Loading</b> -

This command reduces all of the route registration into a single method call within a cached file, improving the performance of route registration when registering hundreds of routes.

```bash
php artisan route:cache
```

- <b>Optimizing View Loading</b> -

To precompile all the Blade views and improve the performance of each request that returns a view.

```bash
php artisan view:cache
```

### Debug Mode

The `APP_DEBUG` value which is stored in .env file should always be set to `false` and `APP_ENV` value to `production` in production environment. Setting the debug value as true may expose some sensitive configuration values to your application end users.

### Environment File Security

The .env file should not be committed to application's source control, as this could be security risk in the event and any sensitive information would be exposed.

### Database and Backup

nmrXiv uses `PostgreSQL` database and details regarding the connection could be established against below values in the .env file.

```bash
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=nmrxiv
DB_USERNAME=****
DB_PASSWORD=****
```

A comprehensive data backup plan should be implemented so that data can be restored and recovered effectively at the time of disaster.

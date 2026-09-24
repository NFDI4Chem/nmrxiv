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

-   <b>Autoloader Optimization</b> -

Below command will optimize Composer's class autoloader map.

```bash
composer install --optimize-autoloader --no-dev
```

:::info Info
Info: In addition to above, the composer.lock file should be present in the project's source control repository, which will make the project's dependencies installation much faster.
:::

-   <b>Optimizing Configuration Loading</b> -

To greatly reduce the number of trips the framework would make to filesystem when loading the configuration files, below command should be executed to combine all the configuration files into a single, cached file.

```bash
php artisan config:cache
```

-   <b>Optimizing Route Loading</b> -

This command reduces all of the route registration into a single method call within a cached file, improving the performance of route registration when registering hundreds of routes.

```bash
php artisan route:cache
```

-   <b>Optimizing View Loading</b> -

To precompile all the Blade views and improve the performance of each request that returns a view.

```bash
php artisan view:cache
```

### Debug Mode

The `APP_DEBUG` value which is stored in .env file should always be set to `false` and `APP_ENV` value to `production` in production environment. Setting the debug value as true may expose some sensitive configuration values to your application end users.

### Environment File Security

The .env file should not be committed to application's source control, as this could be security risk in the event and any sensitive information would be exposed.

### Hosting, Data Backup, Retention, and Archiving

nmrXiv is deployed on virtual machines hosted by the Friedrich Schiller University Jena University Computing Center (FSU URZ). The application services run on these URZ-managed virtual machines, while persistent research data is stored in the FSU URZ S3-compatible object storage service.

nmrXiv uses a `PostgreSQL` database. Connection details are configured through the following `.env` values and must not be committed to source control:

```bash
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=nmrxiv
DB_USERNAME=****
DB_PASSWORD=****
```

#### Backup scope

The repository's persistent data is protected at the FSU URZ data center:

-   Uploaded research files and generated data artifacts are stored in the URZ S3-compatible object storage.
-   The PostgreSQL database, including repository metadata, relationships, permissions, and processing metadata, is backed up to the URZ S3-compatible storage.
-   The application runs on URZ-hosted virtual machines; the virtual machines provide the runtime platform and are not the authoritative copy of research data.
-   The routine Laravel backup job creates a PostgreSQL-only backup. Application source code and rebuildable dependencies are maintained through version control and deployment artifacts rather than being included in the routine database backup.

Database backup archives are compressed and stored in the Ceph-backed S3-compatible storage. Backup operations and cleanup are scheduled by the application, and backup health is monitored against the same storage destination.

#### Retention policy

The backup cleanup policy uses a tiered retention ladder to balance recoverability with storage quota:

-   Keep all daily database backups for 7 days.
-   Keep daily recovery points for a further 7 days.
-   Keep one weekly recovery point for 4 weeks.
-   Keep one monthly recovery point for 2 months.
-   Keep one yearly recovery point for up to 10 years.
-   Enforce a maximum managed backup size of approximately 300 GB through `BACKUP_MAX_STORAGE_MB=300000`.

The storage threshold is an application cleanup threshold, not a replacement for the Ceph bucket quota. When the threshold is exceeded, the oldest eligible backups are removed. The newest backup is protected by the backup cleanup strategy, so the actual usage can temporarily exceed the threshold when a single backup is larger than the remaining budget.

#### Recovery and archival principles

The backup policy follows these operational principles:

-   Backups must be restorable, not merely present in object storage. Periodic restore tests should be performed in an isolated environment.
-   Backup archives should be integrity-checked and monitored for age, availability, and storage usage.
-   Access to backup objects must be restricted to authorized operators and service accounts.
-   Retention and deletion must preserve published research data and its metadata according to the repository's preservation commitments. The cleanup policy applies to operational database backup copies, not to published research objects.
-   Long-term preservation is supported by stable identifiers, versioning, downloadable BagIt packages, and checksums for published samples. Operational backups provide disaster recovery and must not be treated as the only archival representation of published research data.

The effective recovery objectives and any additional off-site or immutable copy should be reviewed with FSU URZ operations. A production backup policy should be periodically tested against the available storage quota and documented recovery time and recovery point objectives.

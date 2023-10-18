# MacOS

nmrXiv project is built with [Laravel](https://laravel.com/8.x) web application framework which comes with [Sail](https://laravel.com/8.x/sail), a built-in solution for running your Laravel project using Docker.
The whole project is a package of below services and features.

- [PostgreSQL](https://www.postgresql.org/)
- [Redis](https://redis.io/)
- [Selenium](https://www.selenium.dev/documentation/)
- [Meilisearch](https://docs.meilisearch.com/)
- [MailHog](https://mailtrap.io/blog/mailhog-explained/)

### Dependencies

- [git](https://git-scm.com/).
- [Docker Desktop](https://www.docker.com/products/docker-desktop).
- [Composer](https://getcomposer.org/)

### Setup:

- Start Docker.
- Open your chosen directory in the terminal.
- Clone the [project from Github](https://github.com/NFDI4Chem/nmrxiv) by running:

```bash
git clone https://github.com/NFDI4Chem/nmrxiv.git
```

- Go to the project directory:

```bash
cd nmrxiv
```

- Navigate to the development branch:

```bash
git checkout development
```

- Create an `.env` file, and copy the existing `.env.example` into it:

```bash
cp .env.example .env 
```

- Update the dependencies from the `composer.json` file:

```bash
composer update
```

- Install the dependencies from the `composer.lock` file:

```bash
composer install
```

- Start [Sail](https://laravel.com/8.x/sail#starting-and-stopping-sail) to build application containers on your machine:

```bash
./vendor/bin/sail up
```

- Run the below command to migrate the database with some dummy values. Don't forget to note down the admin's user id and password provided at the end of migration output.

```bash
./vendor/bin/sail artisan migrate:refresh --seed
```

- Open another terminal and run the below command to boot up your local static web server.

```bash
npm install && npm run dev
```

- For all background jobs to run, nmrXiv is powered with [Redis](https://redis.com/) and packaged with [Horizon](https://github.com/laravel/horizon).
  Run the below command to publish all the jobs and start the worker for the background jobs to execute.

```bash
./vendor/bin/sail artisan horizon:publish
./vendor/bin/sail artisan horizon
```

- To configure file object storage, you should have [Minio](https://min.io/) instance already running in your local(for more details check your docker-compose file). For the first time you have to generate the Access Keys, create the buckets and configure that in your `.env` file.
  - Open the Minio instance running in your [http://localhost:8900](http://localhost:8900/)
  - Login with user - `sail` and password - `password`
  - Go to Access Keys and create a new access key.
  - Create the two buckets with Read Write Access as `nmrxiv` and `nmrxiv-public`
  - Update the Filesystem driver and the AWS Keys as below in the `.env` file. Make sure you point your `AWS_URL` to Minio API endpoint which is running in port 9000. Please define both the public and private driver. The public bucket is used to store all the files which can be publicly accessible through out the application, such as profile & project images. All the private files uploaded by the user will end up in the private bucket.

```bash
FILESYSTEM_DRIVER=minio
FILESYSTEM_DRIVER_PUBLIC=minio_public

AWS_ACCESS_KEY_ID=**********
AWS_SECRET_ACCESS_KEY=************
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=nmrxiv
AWS_ENDPOINT=http://localhost:9000/
AWS_URL=http://localhost:9000/
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_BUCKET_PUBLIC=nmrxiv-public
```

Once the application's Docker containers have been started, you can access the application in your web browser at [http://localhost](http://localhost). But first, you will be prompted to <b>Generate app key</b>. After pressing the generation button, the following message is shown on the screen: "The solution was executed successfully. Refresh now." After refreshing, you access the application.

:::caution Recommendation
Follow our [code contribution guidelines](/developer-guides/code-contribution-guidelines) to make a pull request.
:::

:::info Info
Click to learn more about [Laravel](https://laravel.com/9.x) and [installation guides](https://laravel.com/9.x/installation).
:::

---
sidebar_position: 3
id: storage
title: Storage
---

Files in [nmrxiv](https://nmrxiv.org) are stored as [objects](https://en.wikipedia.org/wiki/Object_storage) and are [S3](https://en.wikipedia.org/wiki/Amazon_S3) compatible which provides below three big benefits.
* **Backup/redundancy** - S3 and similar have built-in backups and redundancy
* **Scaling** - Savings files off-server becomes necessary in modern hosting, such as serverless or containerized environments, as well as in traditional load-balanced environments
* **Disk usage** - You won't need as much disk space when storing files in the cloud
* **Features** - S3 (and other clouds) have some great features, such as versioning support for files, lifecycle rules for deleting old files (or storing them in a cheaper way), deletion protection, and more

#### Configuration
There are just two place where the configuration needs to be done.
* Within your Object Storage or Amazon S3 you need to.
    * Create the bucket.
    * Create a service account to get Key/Secret Key.

* Within the [nmrxiv](https://nmrxiv.org) you just need to configure below values in the `.env` file.
````
FILESYSTEM_DRIVER=

AWS_ACCESS_KEY_ID=xxxxxx
AWS_SECRET_ACCESS_KEY=xxxxxxs
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=nmrxiv
AWS_BUCKET_PUBLIC=nmrxiv-public
AWS_ENDPOINT=<end-point-of-your-storage-provider>
AWS_URL=<end-point-of-your-storage-provider>

````

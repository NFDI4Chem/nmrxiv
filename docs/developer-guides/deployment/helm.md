# Helm Chart
nmrXiv is packaged and published as [Helm](https://helm.sh/) for Kubernetes deployment, which makes the installation easy to define, install, and upgrade.
You need to install Helm first to use the charts. Please refer to the Helm's [documentation](https://helm.sh/docs) to get started.

The chart comes with following optional dependencies which you can opt to have in your deployment if you wish to:
* [Meilisearch](https://docs.meilisearch.com/) (Search Engine)
* [RabbitMQ](https://www.rabbitmq.com/documentation.html) (Message Broker)
* [Redis](https://redis.io/documentation) (Cache)


Once Helm has been set up correctly, add the repo as follows:

```bash
helm repo add repo-helm-charts https://nfdi4chem.github.io/repo-helm-charts/
```

If you had already added this repo earlier, run `helm repo update` to retrieve
the latest versions of the packages.  You can then run `helm search repo
repo-helm-charts` to see the charts.

Before you install [generate your own application key](https://stackoverflow.com/questions/33370134/when-to-generate-a-new-application-key-in-laravel) and provide that value in the .Values.appProperties.key property.

To install the nmrxiv-app chart:

    helm install my-nmrxiv-app repo-helm-charts/nmrxiv-app

To uninstall the chart:

    helm delete my-nmrxiv-app


To learn more about the structure of the chart, visit our [Github repo](https://github.com/NFDI4Chem/repo-helm-charts).

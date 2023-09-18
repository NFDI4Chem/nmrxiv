# Google Kubernetes Engine (GKE)
nmrXiv application is deployed and setup in Google Kubernetes Engine. This page provides the complete guide on how the current deployment is setup in Google Cloud Platform.

### Google Artifact Registry:
nmrXiv docker container images are public and is stored in Google Artifact. 
To pull the latest image for application and Nginx use the below command:

Application
```bash
docker pull \
    europe-west3-docker.pkg.dev/nmrxiv/nmrxiv-prod/nmrxiv-app:latest
```

Nginx
```bash 
docker pull \
    europe-west3-docker.pkg.dev/nmrxiv/nmrxiv-prod/nmrxiv-nginx:latest
```
Steps to configure the Artifact Registry:
1. Enable the Artifact Registry API
2. [Setup](https://cloud.google.com/sdk/docs/install) gcloud CLI in your local system.
3. [Authenticate](https://cloud.google.com/artifact-registry/docs/docker/pushing-and-pulling#auth) your repository to allow Docker to have access.
```bash 
    gcloud auth configure-docker europe-west3-docker.pkg.dev
```
4. Push the image:

    * Build the docker image locally:
        ```bash
        docker build -f ./resources/ops/docker/app/app.dockerfile .
        ```
    * Tag the image with repository name:
        ```bash
        docker tag <image id> europe-west3-docker.pkg.dev/nmrxiv/nmrxiv-prod/nmrxiv-app
        ```
    * Push the image:
        ```bash
        docker push  europe-west3-docker.pkg.dev/nmrxiv/nmrxiv-prod/nmrxiv-app
        ```

### Cluster Configuration:
To start with, create a GKE-Standard Cluster with e2-standard-2(dual core, 8GB memory) machine type (or higher) with single or multiple Node Pool. Choose the other basic options as relevant such as Location and zone of your resources, Boot Disk size and type, Maximum Nodes per node etc. 
Once your cluster is ready open the cloud shell and configure the kubectl command line access to your cluster.
Helm is already installed in Google Cloud Shell to check the version type 
```bash
    helm version
```
### Deploy with Helm:
Next step is to just add the repo as follows and install the chart.
Follow the steps provided [here](https://docs.nmrxiv.org/docs/developer-guides/deployment/helm) to install the chart using helm.
You might need to have your own values.yml file instead of using the default one. 
Once the helm deployment is successfully completed you can check the status of your resources in the workloads and services tab. 

### Ingress Setup:
Your services are deployed but not exposed to the internet yet. Kubernetes allows administrators to bring their own Ingress Controllers instead of using the cloud provider's built-in offering. So to do so we can use Nginx or any other server of your choice. But here we are using Nginx(deployed via helm). Deploy and configure Nginx by following the steps provided in the [link](https://cloud.google.com/community/tutorials/nginx-ingress-gke). We have already done a few steps so you can directly jump to the Deploy ingress controller section.
You might have to create your own ingress-resource file by taking the reference from below.

```bash
    apiVersion: networking.k8s.io/v1
    kind: Ingress
    metadata:
    name: ingress-resource-dev
    annotations:
        kubernetes.io/ingress.class: "nginx"
        nginx.ingress.kubernetes.io/ssl-redirect: "true"
        cert-manager.io/issuer: "letsencrypt-dev"

    spec:
    tls:
    - hosts:
        - dev.nmrxiv.org
        secretName: nmrxiv-app-dev-tls

    rules:
    - host: dev.nmrxiv.org
        http:
        paths:
        - pathType: Prefix
            path: "/"
            backend:
            service:
                name: nmrxiv-nmrxiv-app
                port:
                number: 80

```

### Certificate Installation:
Now that the ingress is established and we can access the application via the domain name in the browser, next step would be to install the TLS or certificate for your domain name. To do so we have various [option](https://kubernetes.github.io/ingress-nginx/user-guide/tls/) but here we have used [cert-manager](https://cert-manager.io/docs/) which will automatically request missing or expired certificates from a range of supported issuer(Let's Encrypt) by monitoring ingress-resource.
1. Install cert-manager using [helm](https://cert-manager.io/docs/installation/helm/).
2. Configure [Let's Encrypt Issuer](https://cert-manager.io/docs/tutorials/acme/nginx-ingress/#step-6---configure-a-lets-encrypt-issuer).
3. Deploy a [TLS Ingress Resource](https://cert-manager.io/docs/tutorials/acme/nginx-ingress/#step-7---deploy-a-tls-ingress-resource).

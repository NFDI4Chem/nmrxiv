# Continous Integration and Continous Deployment 

To simplify the process of development, releasing of software, and also keeping the [agile](https://en.wikipedia.org/wiki/Agile_software_development) development practices in mind, we have a [CI/CD](https://en.wikipedia.org/wiki/CI/CD) workflow intact with our repository which will enforce the automation of the whole deployment process. 
The workflow focuses on automating the software delivery process so that the team can easily deploy their code to [production](https://www.nmrxiv.org/) at any time without compromising on quality.

We have used [Github Actions](https://docs.github.com/en/actions) for building the [CI/CD](https://en.wikipedia.org/wiki/CI/CD) pipeline and below is the diagram to briefly describe how the current setup works. The workflow gets triggered the moment the code is pushed to the respective branches ([dev-helm-deploy](https://github.com/NFDI4Chem/nmrxiv/tree/dev-helm-deploy) to deploy code to [development](https://dev.nmrxiv.org) and [prod-helm-deploy](https://github.com/NFDI4Chem/nmrxiv/tree/prod-helm-deploy) to deploy code to [production](https://www.nmrxiv.org/)). Once the workflow is triggered (which you can find in the Actions tab) it will perform the below actions.
* **Test** - Execute automated tests(WIP).
* **Build and Push** -  Build the Docker images and push to [Google Artifact Registry](https://cloud.google.com/artifact-registry/docs).
* **Deploy** - Pull the latest image, deploy and restart the deployment in the [Google Kubernetes Engine](https://cloud.google.com/kubernetes-engine/docs) cluster.

All the jobs defined above are synchronous and failure at any step will abort the consecutive jobs. After the successful completion of the workflow, the code changes will be reflected on the server with minimum or zero downtime.

Click [here](/docs/developer-guides/deployment/environment) to learn more about our current infrastructure environment.

![CI/CD Worflow](/img/cicd_workflow.jpg) 


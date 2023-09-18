
# Code Contribution Guidelines
Thank you for investing your time in contributing to our project! Any contribution you make will be reflected on [nmrxiv.org](https://nmrxiv.org) after it has passed all the test cases on [dev.nmrxiv.org](https://dev.nmrxiv.org). 
In this guide you will get an overview of the contribution workflow from opening an issue, creating a PR, reviewing, and merging the PR.

### Getting started
Before you start contributing, make sure:
* You have a GitHub account, for information on how to create an account, see [Signing up for GitHub.](https://docs.github.com/en/get-started/signing-up-for-github).
* You have proper access to the [repo](https://github.com/NFDI4Chem/nmrxiv) for your GitHub account, for which you can contact repository owners.
* You have a code editor installed in your local system, we recommend using [Visual Studio Code](https://code.visualstudio.com/).
* You have cloned the code and have nmrXiv running locally, for which you can follow the steps provided [here](https://docs.nmrxiv.org/docs/developer-guides/installation/development).


#### Create a new issue
If you spot a problem with the application, [search if an issue already exists](https://github.com/NFDI4Chem/nmrxiv/issues). If a related issue doesn't exist, you can open a [new issue](https://github.com/NFDI4Chem/nmrxiv/issues/new/choose) with the appropriate form.
[Click here](/docs/FAQs#how-to-report-a-bug) to learn more about how to create an issue or report a bug.

#### Solve an issue
Scan through our [existing issues](https://github.com/NFDI4Chem/nmrxiv/issues) to find one that interests you. You can narrow down the search using `labels` as filters.

### Workflow

#### Create a branch
Never push your code directly to the `development` or `main` branch. Switch to the `development` branch and create a new branch in your repository. A short, descriptive branch name enables your collaborators to see ongoing work at a glance. The branch name should be all small with words separated by a hyphen, [click here](https://github.com/NFDI4Chem/nmrxiv/branches) to follow some examples. 

#### Make Changes
On your branch, make the desired changes to the repository. Your branch is a safe place to make changes. If you make a mistake, you can revert your changes or push additional changes to fix the mistake. Your changes will not end up on the default branch until you merge your branch. Commit and push your changes to your branch. Once your changes are ready, don't forget to self-review to speed up the review process. Also, you have proper comments all over your code so that it's easy and clean to understand. 

#### Format
For formatting, you can simply run below command which will help saving your time in code formatting and makes it simple to ensure that your code style stays clean, consistent and properly indented.
```bash
npm run format
./vendor/bin/sail pint
```

#### Commit 
To facilitate auto CHANGELOG generation, creation of GitHub releases, and version bumps(as per [Semantic Versioning](https://semver.org/)), please follow the structure of [Conventional Commit](https://www.conventionalcommits.org/en/v1.0.0/) for your commit messages.
Each commit should also be descriptive enough to help you and future contributors to understand what changes the commit contains.
```bash
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```
choose your `<type>` from below:
1. `fix` : a commit of the type fix patches a bug in your codebase (this correlates with PATCH in Semantic Versioning).
2. `feat` : a commit of the type feat introduces a new feature to the codebase (this correlates with MINOR in Semantic Versioning).
3. `BREAKING CHANGE` :  a commit that has a footer BREAKING CHANGE:, or appends a ! after the type/scope, introduces a breaking API change (correlating with MAJOR in Semantic Versioning). A BREAKING CHANGE can be part of commits of any type.

An example would look like this:
```bash
fix: update iframe reference point

Resolves the connection issue for the upload view. 

Reviewed-by: XYZ
Fixes: #123
```
Click here to learn more about [Conventional Commit](https://www.conventionalcommits.org/en/v1.0.0/).

#### Create a pull request
When you're finished with the changes, create a pull request(also known as a PR) to the `development` branch.
When you create a pull request, make sure
* You include a summary of the changes and what problem they solve. 
* Your branch has no conflicts with the base branch.
* You have [linked the issue](https://docs.github.com/en/issues/tracking-your-work-with-issues/linking-a-pull-request-to-an-issue), if your pull request addresses an issue. If you link with a keyword, the issue will close automatically when the pull request merges.
* You have assigned a reviewer for faster code review.
<img src="/img/pull_request.gif"/>

#### Address review comments
Reviewers can leave questions, comments, and suggestions. Reviewers can comment on the whole pull request or add comments to specific lines. You and reviewers can insert images or code suggestions to clarify comments.
You can continue to commit and push changes in response to the reviews. Your pull request will update automatically.

#### Merge your pull request
Your pull request can be merged once it is approved by the reviewer. The primary merge should always be made to the `development` branch, from where your code will be deployed to the [dev environment](https://dev.nmrxiv.org) via our [CI/CD pipeline](https://docs.nmrxiv.org/docs/developer-guides/ci-cd). The [dev environment](https://dev.nmrxiv.org) provides the room to test your feature or code changes. Once it has passed all the test cases, your code changes will now be included as part of a release and be deployed finally to the [production environment](https://nmrxiv.org). These actions are restrictive and should only be performed by our repo admins and owners. 

#### Delete your branch
After your branch is merged and the pull request is closed please don't forget to delete your stale branch. This indicates that the work on the branch is complete and prevents you or others from accidentally using old branches. For more information, see [Deleting and restoring branches in a pull request](https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-branches-in-your-repository/deleting-and-restoring-branches-in-a-pull-request).
<img src="/img/delete_branch.png"/>

:::info Info
* Never leak your secrets or commit local config files(.env) into source control.
* Follow the link to learn more about [Laravel](https://laravel.com/docs/9.x/readme) and [Inertia.js](https://inertiajs.com/) best practices.
:::

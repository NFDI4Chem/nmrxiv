---
sidebar_position: 2
id: oauth
title: OAuth
---

In addition to basic username/password authentication, nmrXiv also supports authentication via [OAuth 2.0](https://www.digitalocean.com/community/tutorials/an-introduction-to-oauth-2), which is an authorization framework that enables third party clients to obtain limited access to user accounts on an HTTP service.
Currently nmrXiv supports login via below three clients:
* [Twitter](https://twitter.com/)
* [GitHub](https://github.com/)
* [ORCID](https://orcid.org/)

In order to get this feature available in your application, you need to register your application with the service. This is done through a registration form in the **developer** or **API** portion of the service's website, where you will provide following information (and probably details about your application):
* Application Name
* Application Website
* Redirect URI or Callback URL

Once your application is registered, the service will issue client credentials in the form of a *Client Id*  and *Client Secret*. These details should be added to the .env file against the below values.
````
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URL=

ORCID_CLIENT_ID=
ORCID_CLIENT_SECRET=
ORCID_REDIRECT_URL=

TWITTER_CLIENT_ID=
TWITTER_CLIENT_SECRET=
TWITTER_REDIRECT_URL=
````
Once done restart or refresh your application and you would be able to find the single sign-on section added in the Login page.

To know more about how nmrXiv works with OAuth visit the official site of [Laravel](https://laravel.com/docs/8.x/socialite) for more details. 

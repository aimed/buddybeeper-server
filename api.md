# General

To make requests to the API you'll need a valid **client id** and **client secret**.

Request can be made using either GET, POST or DELETE methods. 
The request body for POST or DELETE requests should be either __application/json__ or __application/x-www-form-urlencoded__ encoded with the corresponding __Content-Type__ header set.

Every reponse you get from the API will be a valid JSON object and structured as below:

```
{
	meta: {
		code: 200,
		status: "200 OK",
		invalid_parameters: null,
		errors: null
	},
	...
}
```

The meta object will give you information about the success or failure of your request. 
__Status__ will be corresponding the the HTTP response status code. Successfull requests are going to be answered with "200 OK". In case an error occured, the __code__ may help you identify the problem. We further provide additional information in case of failed requests.
__Invalid parameters__ should give you a list of parameters that were required/accepted but seemed to be invalid.
__Errors__ will contain a list of human readable error messages.


# Authentication

To __make requests on a users behalft__, you will need to pass a valid __access token__. Access tokens are valid for an hour and may be passed either as the ```access_token``` **url parameter** or as the ```X-Access-Token``` **header**.

## Refresh Token
In order to obtain a __access token__, you must request a __refresh token__ first.

```
POST /auth/token
```

- ```client_id```
- ```client_secret```
- ```username```
- ```password```

```
{
	refresh_token: "XXX",
	access_token: "XXX",
	expires_at: 137000000
	user: {
		id: 1,
		first_name: "John",
		last_name: "Doe"
	}
}
```

## Access Token
Once you have a valid __refresh token__, you can request __access tokens__.

```
POST /auth/refresh
```

- ```refresh_token```

```
{
	access_token: "XXX",
	expires_at: 137000000
	user: {
		id: 1,
		first_name: "John",
		last_name: "Doe"
	}
}
```
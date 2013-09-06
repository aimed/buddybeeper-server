# General

To make requests to the API you'll need a valid **client id** and **client secret**.

Request can be made using either GET, POST or DELETE methods. 
The request body for POST or DELETE requests should be either __application/json__ or __application/x-www-form-urlencoded__ encoded with the corresponding __Content-Type__ header set.

Every reponse you get from the API will be a valid JSON object and structured as below:

```
{
	"meta": {
		"code": 200,
		"status": "200 OK",
		"invalid_parameters": null,
		"errors": null
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
	"refresh_token": "XXX",
	"access_token": "XXX",
	"expires_at": 137000000
	"user": {
		"id": 1,
		"first_name": "John",
		"last_name": "Doe"
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
	"access_token": "XXX",
	"expires_at": 137000000
	"user": {
		"id": 1,
		"first_name": "John",
		"last_name": "Doe"
	}
}
```

# User

You can get information about a certain user by one of the following endpoints. You will have to pass a valid __access token__ or __event token__ (see below). If you want to request information about the authenticated user, you may also replace the __USER_ID__ with "__me__".

## Create

Creating users may result 

```
POST /users
```

- ```email```
- ```password```
- ```first_name```
- ```last_name```

```
{
	"status": "ok",
	"user": {
		"id": 1,
		"first_name": "John",
		"last_name": "Doe"
	}
}
```


## About

```
GET /users/:USER_ID
```

```
{
	"id": 1,
	"first_name": "John",
	"last_name": "Doe"
}
```

## Events

```
GET /users/:USER_ID/events
```

```
{
	"events": [
		{
			"event_token": "XXX",
			"title": "Unicorn meeting",
			"description": "Trying to gather all unicorns. Are you in?",
			"created_at": "2012-12-29 18:30:00",
			"host": {
				"id": 1,
				"first_name": "John",
				"last_name": "Doe"
			},
			"invites": [
				{
					"id": 1,
					"first_name": "John",
					"last_name": "Doe"
				}
			],
			"dates": [
				{
					"start": "2013-01-01 00:00:00",
					"end": "2013-01-01 01:00:00",
					"votes": [
						{
							"id": 1,
							"first_name": "John",
							"last_name": "Doe"
						}
					]
				}
			],
			"activities": [
				{
					"name": "Meeting",
					"votes": []
				}
			],
			"discussion": [
				{
					"user": {
						"id": 1,
						"first_name": "John",
						"last_name": "Doe"
					},
					"text": "ALL MIGHT TO THE UNICORNS!",
					"created_at": "2012-12-30 18:30:00"
				}
			]
		}
	]
}
```
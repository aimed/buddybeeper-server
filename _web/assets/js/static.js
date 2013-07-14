function success (o) {
	return {response:o};
}
function error (o) {
	return {error:o}
}
var static = {};
static.users = [
{
	id			  : 1,
	first_name    : "Max",
	last_name     : "Appleseed",
	profile_image : "/assets/img/default_user_image.png",
	events		  : [
		{
			id          : 1,
			host		: {
				id			  : 1,
				first_name    : "Max",
				last_name     : "Appleseed",
				profile_image : "/assets/img/default_user_image.png"
			},
			title       : "Zombie hunting?",
			description : "Hey guys, we haven't been zombie hunting in a while.",
			invites     : [
				{
					id			  : 1,
					first_name    : "Max",
					last_name     : "Appleseed",
					profile_image : "/assets/img/default_user_image.png"
				},
				{
					id			  : 2,
					first_name    : "Peter",
					last_name     : "Bob",
					profile_image : "/assets/img/default_user_image.png",
				},
				{
					id			  : 3,
					first_name    : "Alice",
					last_name     : "Whothef",
					profile_image : "/assets/img/default_user_image.png",
				}
				
			],
			dates       : [{start:new Date(),votes:[1]}],
			activities  : [{name:"Zombie hunting",votes:[]},{name:"Eat brain",votes:[2]}],
			comments : [
				{
					user:{
						id			  : 1,
						first_name    : "Max",
						last_name     : "Appleseed",
						profile_image : "/assets/img/default_user_image.png"
					},
					text : "Greate idea!"
				}
			]
		}
	]
},
{
	id			  : 2,
	first_name    : "Peter",
	last_name     : "Bob",
	profile_image : "/assets/img/default_user_image.png",
	events		  : []
}
];


static.ping = {fail : null, success: {access_token: "sometokenstring123", user: static.users[0]}, should: true};
static.loginResponse = {fail: {details:"Invalid username"}, success: {access_token: "sometokenstring123", user: static.users[0]}};

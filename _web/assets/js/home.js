bb.controller("event", ["$scope", "Event",  function (scope, Event) {

	var event = new Event(scope.event.token,scope.user);
	
	scope.showPeopleList  = false;
	scope.limitPeopleList = 2;
	scope.mode			  = scope.event.id ? "compact" : "edit";
	scope.edit	 		  = function () { scope.mode = "edit"; scope.backup.store(); }
	scope.expand 		  = function () { scope.mode = "expanded"; scope.backup.recover(); }
	scope.compact 		  = function () { scope.mode = "compact"; scope.backup.recover(); }
	scope.isHost		  = function () { return scope.event.host.id == scope.user.id; }
	scope.parseDate		  = function (d) { 
		return (typeof d !== "string") ? d : new Date(Date.parse(d.replace("-","/"))); 
	}
	scope.event.created_at = scope.parseDate(scope.event.created_at);
	scope.hasVotedOn		 = function (item) { return !item.votes || item.votes.indexOf(scope.user.id) > -1; }
	scope.togglePeopleList   = function (bool) { 
		scope.showPeopleList = bool; scope.limitPeopleList = bool ? scope.event.invites.length : 2; 
	}
	scope.toggleVote = function (item, type) {
		if (scope.hasVotedOn(item, type)) {
			item.votes.splice(item.votes.indexOf(scope.user.id),1);
			event.unvote(item,type);
		} else {
			item.votes.push(scope.user.id);
			event.vote(item,type);
		}
	}
	scope.addComment = function (comment) {
		scope.event.comments.push(event.comment(comment.text));
	}
	scope.addDate = function (date) {
		date.votes = [scope.user.id];
		if (scope.event.dates.indexOf(date) === -1) scope.event.dates.push(date);
		if (scope.event && scope.event.id) event.createDate(date.start,function (r) { date.id = r.id; });
	}
	scope.addActivity = function (activity) {
		activity.votes = [scope.user.id];
		if (scope.event.activities.indexOf(activity) === -1) scope.event.activities.push(activity);
		if (scope.event && scope.event.id) event.createActivity(activity.name,function (r) { activity.id = r.id; });
	}
	scope.invite = {
		submit : function () {
			var data = {
				first_name:this.first_name,
				last_name:this.last_name,
				email:this.email,
				profile_image:"/assets/img/default_profile_image.png"
			};
			scope.event.invites.push(data);
			if (scope.event.id) {
				event.invite([data], function (r) { 
					if (r && r.response) scope.event.invites = r.response.invites; 
				});
			}
			this.first_name = "";
			this.last_name = "";
			this.email = "";
			scope.limitPeopleList = scope.event.invites.length;
		}
	}
	scope.createEvent = function (e) {
		console.log(e, scope.accessToken);
		event.create(e, function (r) {
			scope.showPeopleList = false;
			scope.expand();
			scope.event.id = r.id;
			scope.event.token = r.token;
			//scope.event.invites = r.invites;
			event = new Event(r.token);
			console.log(scope.event);
			for (ac in e.activities) {
				scope.addActivity(e.activities[ac]);
			}
			for (da in e.dates) {
				scope.addDate(e.dates[da]);
			}
		});
	}
	scope.saveEvent = function () {
		scope.mode = "expanded";
	}
	scope.backup = {
		store : function () {
			scope.backup.item = angular.copy(scope.event);
		},
		recover : function () {
			if (scope.backup.item) {
				scope.event = scope.backup.item;
				scope.backup.item = null;
			}
		}
	}
	
	
}]);


bb.controller("feed",["$scope", "$location", "User", function (scope, location, user) {
	scope.calculateEventPriority = function (event) {
		return !event.id || event.isNew ? 0 : event.id;
	}
	scope.setEvents = function () {
		var searchResults = location.search();
		if (searchResults.token) {
			console.log(searchResults.token);
			user.getMe(searchResults.token, function (r) {
				if (r.response && r.response.events) {
					scope.events = r.response.events;
					scope.user   = r.response;
				}
			});
			
		} else if (scope.isLoggedIn) {
			scope.events = scope.user.events;
		}
	}
	scope.$on("$routeUpdate",scope.setEvents);
	scope.setEvents();
}]);


bb.controller("landingPage", ["$scope", "User", "$location", function (scope, user, location) {
		
	
	scope.setSignUpFormMode = function (mode) { scope.signUpFormMode = mode; }

	scope.login = function (form) {
		
		response = user.login(form, function (r) {
			if (r.code == 1003) form.error = "Invalid e-mail or password"; 
		});
		scope.$emit("loginstatechange",response);

	}
	
	scope.signup = function (form) {
		console.log("email:",form.email);
		response = user.signup(form, function (r) {
			if (r.code == 1003) form.error = "Invalid field";
			else if (r.response.status != "ok") {
				form.error = "You have already signed up and need to verify your email address.";
			}
		});
		scope.$emit("loginstatechange",response);
	}
	
}]);

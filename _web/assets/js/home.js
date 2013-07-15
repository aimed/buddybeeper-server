bb.controller("event", ["$scope", "Event", function (scope, Event) {

	var event = new Event(scope.event.token);
	
	scope.showPeopleList  = false;
	scope.limitPeopleList = 2;
	scope.mode			  = scope.event.id ? "compact" : "edit";
	scope.edit	 		  = function () { scope.mode = "edit"; scope.backup.store(); }
	scope.expand 		  = function () { scope.mode = "expanded"; scope.backup.recover(); }
	scope.compact 		  = function () { scope.mode = "compact"; scope.backup.recover(); }
	scope.isHost		  = function () { return scope.event.host.id == scope.user.id; }
	scope.parseDate		  = function (d) { return new Date(d); }
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
		scope.event.dates.push(date);
		if (scope.event && scope.event.id) event.createDate(date.start,function (r) { date.id = r.id; });
	}
	scope.addActivity = function (activity) {
		activity.votes = [scope.user.id];
		scope.event.activities.push(activity);
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
			event.invite([data], function (r) {
				console.log(r);
			});
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
			event = new Event(r.token);
			console.log(scope.event);
			for (ac in e.activities) {
				scope.addActivity(ac.name);
			}
			for (da in e.dates) {
				scope.addDate(da.start);
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


bb.controller("feed",["$scope", function (scope) {
	scope.calculateEventPriority = function (event) {
		return !event.id || event.isNew ? 0 : event.id;
	}
}]);


bb.controller("landingPage", ["$scope","User", function (scope, user) {
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
			if (r.code == 1003) form.error = r.details;
			else if (r.response.status != "ok") {
				form.error = "You have already signed up and need to verify your email address.";
			}
		});
		scope.$emit("loginstatechange",response);
	}
	
}]);

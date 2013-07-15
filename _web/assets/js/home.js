bb.controller("event", ["$scope", function (scope) {
	scope.showPeopleList  = false;
	scope.limitPeopleList = 2;
	scope.mode 			  = "compact";
	scope.isHost 			= function () { return scope.event.host.id == scope.user.id; }
	scope.hasVotedOn 		= function (item) { return item.votes.indexOf(scope.user.id) > -1; }
	scope.togglePeopleList 	= function (bool) { 
		scope.showPeopleList = bool; scope.limitPeopleList = bool ? scope.event.invites.length : 2; 
	}
	scope.toggleVote = function (item, event, type) {
		if (scope.hasVotedOn(item,event,type)) {
			item.votes.splice(item.votes.indexOf(scope.user.id),1);
		} else {
			item.votes.push(scope.user.id);
		}
	}
	scope.addComment = function () {
		scope.event.comments.push({text:scope.commentText,user:scope.user,id:scope.event.comments.length*2});
		scope.commentText = "";
	}
	scope.addDate = function (date) {
		date.votes = [scope.user.id];
		scope.event.dates.push(date);
	}
	scope.addActivity = function (activity) {
		activity.votes = [scope.user.id];
		scope.event.activities.push(activity);
	}
	scope.invite = {
		submit : function () {
			scope.event.invites.push({
				first_name:this.first_name,
				last_name:this.last_name,
				id:scope.event.invites.length+1
			});
			this.first_name = "";
			this.last_name = "";
			this.email = "";
			scope.limitPeopleList = scope.event.invites.length;
		}
	}
	scope.saveEvent = function () {
		scope.mode = "expanded";
	}
}]);

bb.controller("landingPage", ["$scope","User", function (scope, user) {
	scope.login = function () {
		response = user.login(scope);
		if (response.details) scope.loginForm.$error.message = response.details;
		scope.$emit("loginstatechange",response);
	}
}]);

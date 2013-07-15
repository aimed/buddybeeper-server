var APIROOT = "http://api.buddybeeper.dev/v0";

/**
 * Factory: Event
 */
bb.factory("Event", ["$rootScope", "$http", function (scope, http) {
	var event = function (token, user) {
		this.token = token;
		this.user  = user || scope.user;
	}
	
	event.prototype.comment = function (text) {
		var response = {text:text,user:this.user};
		http.post(APIROOT + "/events/comment",{text:text},{headers:{"X-Event-Token":this.token}})
		.success(function (r) {
			if (r.response) {
				response = r.response;
			}
		}).error(function () {});
		
		return response;
	}
	
	
	// @TODO: move together
	event.prototype.vote = function (item, type) {
		if (!item.id) return;
		http.post(APIROOT + "/events/" + type + "/" + item.id, {}, {headers:{"X-Event-Token":this.token}})
		.success(function (r) { console.log("voted", r); });
	}
	event.prototype.unvote = function (item, type) {
		if (!item.id) return;
		// @TODO: lolwhat. delete doesnt seem to pass header or body
		http.delete(APIROOT + "/events/" + type + "/" + item.id + "?token=" + this.token, {token:this.token}, {headers:{"X-Event-Token":this.token}})
		.success(function (r) { console.log("unvoted", r); });
	}
	event.prototype.createDate = function (start,cb) {
		start = new Date(start);
		start = [start.getFullYear(),start.getMonth()+1,start.getDate()].join("-") + " 00:00:00";
		http.post(APIROOT + "/events/date", {start:start}, {headers:{"X-Event-Token":this.token}})
		.success(function (r) { console.log("create date", arguments); if(cb) cb(r.response);});
	}
	event.prototype.createActivity = function (name,cb) {
		http.post(APIROOT + "/events/activity", {name:name}, {headers:{"X-Event-Token":this.token}})
		.success(function (r) { console.log("create activity", arguments); if(cb) cb(r.response);});
	}
	event.prototype.create = function (data,cb) {
		var self = this;
		var send = {};
		var host = data.invites.splice(0, 1); 

		send.title = data.title;
		send.description = data.description;
		send.invites = data.invites;

		http.post(APIROOT + "/events", send, {headers:{"X-Access-Token":scope.accessToken}})
		.success(function(r){
			console.log("create event", arguments);
			if (r && r.response && r.response.token) {
				r = r.response;
				data.id = r.id;
				data.token = r.token;
				self.token = r.token;
				if (cb) cb(r);
			}
			data.invites.unshift(host[0]); // @TODO: what?
		});
		
	}
	
	event.prototype.invite = function (list) {
		console.log(list);
		http.post(APIROOT + "/events/invite",list, {headers:{"X-Access-Token":scope.accessToken}})
		.success(function (r) {
			console.log(arguments);
		});
	}
	
	return event;
	
}]);


/**
 * Factory: User
 *
 *
 */
bb.factory("User", ["$rootScope", "$http", function (scope, http) {
	scope.user = {};
	scope.isLoggedIn = false;
	
	scope.$on("loginstatechange", function (event,state) {
		if (state) state = state.response;
		scope.isLoggedIn = !!(state && state.access_token);
		scope.accessToken = scope.isLoggedIn ? state.access_token : "";
		scope.user = (state && state.user) ? state.user : {};
	});
	
	var user = {
		ping : function () {
			http.get("/ping").success(function (r) {
				scope.$emit("loginstatechange",r);
			});
		},
		
		login : function (form,cb) {
			var response = {};
			
			http.post("/login",{email:form.email,password:form.password})
			.success(function (r) {
				response = r;
				scope.$emit("loginstatechange",response);
				if (cb) cb(r);
			});
			
			return response;
		},
		
		signup : function (form,cb) {
			var response = {};
			
			http.post("/register",{
				email:form.email,
				password:form.password,
				first_name:form.first_name,
				last_name:form.last_name
			}).success(function (r) {
				response = r;
				scope.$emit("loginstatechange",response);
				if (cb) cb(r);
			});
			
			return response;
		}
	}
	
	user.ping();
	return user;
}]);
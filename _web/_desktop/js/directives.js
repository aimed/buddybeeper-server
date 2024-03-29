bb.directive("bgImage", function () {
return {
	scope: {bgImage: "@"},
	link : function (scope, elem, attrs) {
		attrs.$observe("bgImage", function (val) {
			elem.css({"background-image":"url(" + val + ")"});	
		});
	}
}
});

bb.directive("inlineEdit", function () {
return {
	replace : true,
	template : '<div><div ng-hide="editWhen()">{{model}}</div><input ng-show="editWhen()" ng-model="model" placeholder="{{placeholder}}" /></div>',
	scope : { editWhen : "&", model : "=editBind", placeholder : "@editPlaceholder"}
}
});

bb.directive("datepicker", function () {
var obj = {
	template : '<table><tr></tr></table>'
}
return function () {}
});
bb.directive("bgImage", function () {
	return function (scope, elem, attrs) {
		elem.css({"background-image":"url(" + attrs.bgImage + ")"});
	}
});

bb.directive("inlineEdit", function () {
	return {
		replace : true,
		template : '<div><div ng-hide="editWhen()">{{model}}</div><input ng-show="editWhen()" ng-model="model" /></div>',
		scope : { editWhen : "&", model : "=editBind"}
	}
});
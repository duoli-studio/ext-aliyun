var alias = {
    "bt3": "assets/js/libs/bt3/3.3.7/bootstrap",
    "jquery": "assets/js/libs/jquery/1.9.1/jquery.min",
    "jquery.form": "assets/js/libs/jquery/form/4.2.2/jquery.form",
    "jquery.layer": "assets/js/libs/jquery/layer/3.1.1/jquery.layer",
    "jquery.toastr": "assets/js/libs/jquery/toastr/2.1.3/jquery.toastr",
    "jquery.validation": "assets/js/libs/jquery/validation/1.17.0/jquery.validation"
};

var shim  = {
    "bt3": [
        "jquery"
    ],
    "jquery.form": [
        "jquery"
    ],
    "jquery.layer": [
        "jquery"
    ],
    "jquery.toastr": [
        "jquery"
    ],
    "jquery.validation": [
        "jquery"
    ]
};

// appends for single project
// noinspection JSUnresolvedVariable
if (typeof appends != 'undefined' && typeof appends == 'object') {
	for (var k in appends) {
		alias[k] = appends[k];
	}
}

// require js config
requirejs.config({
	baseUrl: "/",
	paths  : alias,
	shim   : shim
});
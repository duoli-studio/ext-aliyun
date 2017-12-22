var alias = {
    "bootstrap": "assets/js/libs/bt3/3.3.7/bootstrap",
    "jquery": "assets/js/libs/jquery/1.9.1/jquery.min",
    "jquery.toastr": "assets/js/libs/jquery/toastr/2.1.3/jquery.toastr"
};

var shim  = {
    "bootstrap": [
        "jquery"
    ],
    "jquery.toastr": [
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
	baseUrl: "/assets/js/",
	paths  : alias,
	shim   : shim
});
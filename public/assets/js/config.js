var alias = {
    "ace": "assets/js/libs/ace/1.2.9",
    "bt3": "assets/js/libs/bt3/3.3.7/bootstrap",
    "bt3.hover-dropdown": "assets/js/libs/bt3/hover-dropdown/2.2.1/bt3.hover-dropdown",
    "clipboard": "assets/js/libs/clipboard/1.7.1/clipboard.min",
    "global": "assets/js/global",
    "jquery": "assets/js/libs/jquery/2.2.4/jquery.min",
    "jquery.form": "assets/js/libs/jquery/form/4.2.2/jquery.form",
    "jquery.image-picker": "assets/js/libs/jquery/image-picker/0.3.0/jquery.image-picker",
    "jquery.layer": "assets/js/libs/jquery/layer/3.1.1/jquery.layer",
    "jquery.metis-menu": "assets/js/libs/jquery/metis-menu/2.7.2/jquery.metis-menu",
    "jquery.slimscroll": "assets/js/libs/jquery/slimscroll/1.3.8/jquery.slimscroll.min",
    "jquery.toastr": "assets/js/libs/jquery/toastr/2.1.3/jquery.toastr",
    "jquery.tokenize2": "assets/js/libs/jquery/tokenize2//jquery.tokenize2",
    "jquery.validation": "assets/js/libs/jquery/validation/1.17.0/jquery.validation",
    "jquery.webuploader": "assets/js/libs/jquery/webuploader/0.1.5/jquery.webuploader",
    "js-cookie": "assets/js/libs/js-cookie/2.2.0/js-cookie",
    "json": "assets/js/libs/json/json2",
    "pace": "assets/js/libs/pace/1.0.2/pace.min",
    "smooth-scroll": "assets/js/libs/smooth-scroll/1.4.0/smooth-scroll",
    "vkbeautify": "assets/js/libs/vkbeautify/vkbeautify"
};

var shim  = {
    "ace": {
        "exports": "ace"
    },
    "bt3": [
        "jquery"
    ],
    "bt3.hover-dropdown": [
        "jquery",
        "bt3"
    ],
    "jquery.form": [
        "jquery"
    ],
    "jquery.image-picker": [
        "jquery"
    ],
    "jquery.layer": [
        "jquery"
    ],
    "jquery.slimscroll": [
        "jquery"
    ],
    "jquery.toastr": [
        "jquery"
    ],
    "jquery.tokenize2": [
        "jquery"
    ],
    "jquery.validation": [
        "jquery"
    ],
    "jquery.webuploader": [
        "jquery"
    ],
    "js-cookie": {
        "exports": "Cookies"
    },
    "json": {
        "exports": "JSON"
    },
    "vkbeautify": {
        "exports": "vkbeautify"
    }
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
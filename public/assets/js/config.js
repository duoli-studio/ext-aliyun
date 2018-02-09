var alias = {
    "ace": "assets/js/libs/ace/1.2.9",
    "autosize": "assets/js/libs/autosize/4.0.0/autosize",
    "bt3": "assets/js/libs/bt3/3.3.7/bootstrap",
    "bt3.data-tables": "assets/js/libs/bt3/data-tables/2.1.1/bt3.data-tables",
    "bt3.hover-dropdown": "assets/js/libs/bt3/hover-dropdown/2.2.1/bt3.hover-dropdown",
    "bt4": "assets/js/libs/bt4/4.0.0/bootstrap",
    "centrifuge": "assets/js/libs/centrifuge/1.4.8/centrifuge",
    "clipboard": "assets/js/libs/clipboard/1.7.1/clipboard.min",
    "datatables.net": "assets/js/libs/jquery/data-tables/1.10.16/jquery.data-tables",
    "global": "assets/js/global",
    "highlight": "assets/js/libs/highlight/9.12.0/highlight",
    "jquery": "assets/js/libs/jquery/2.2.4/jquery.min",
    "jquery.form": "assets/js/libs/jquery/form/4.2.2/jquery.form",
    "jquery.layer": "assets/js/libs/jquery/layer/3.1.1/jquery.layer",
    "jquery.metis-menu": "assets/js/libs/jquery/metis-menu/2.7.2/jquery.metis-menu",
    "jquery.slimscroll": "assets/js/libs/jquery/slimscroll/1.3.8/jquery.slimscroll.min",
    "jquery.toastr": "assets/js/libs/jquery/toastr/2.1.3/jquery.toastr",
    "jquery.tokenize2": "assets/js/libs/jquery/tokenize2//jquery.tokenize2",
    "jquery.validation": "assets/js/libs/jquery/validation/1.17.0/jquery.validation",
    "jquery.webuploader": "assets/js/libs/jquery/webuploader/0.1.5/jquery.webuploader",
    "js-cookie": "assets/js/libs/js-cookie/2.2.0/js-cookie",
    "pace": "assets/js/libs/pace/1.0.2/pace.min",
    "requirejs": "assets/js/libs/requirejs/require",
    "smooth-scroll": "assets/js/libs/smooth-scroll/1.4.0/smooth-scroll",
    "sockjs": "assets/js/libs/sockjs/0.3.4/sockjs",
    "underscore": "assets/js/libs/underscore/1.8.3/underscore",
    "vkbeautify": "assets/js/libs/vkbeautify/vkbeautify"
};

var shim  = {
    "ace": {
        "exports": "ace"
    },
    "bt3": [
        "jquery"
    ],
    "bt3.data-tables": [
        "jquery",
        "bt3"
    ],
    "bt3.hover-dropdown": [
        "jquery",
        "bt3"
    ],
    "bt4": [
        "jquery"
    ],
    "datatables.net": [
        "jquery"
    ],
    "highlight": {
        "exports": "hljs"
    },
    "jquery.form": [
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
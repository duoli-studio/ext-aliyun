var alias = {
    "ace": "resources/js/libs/ace/1.2.9",
    "autosize": "resources/js/libs/autosize/4.0.0/autosize",
    "bt3": "resources/js/libs/bt3/3.3.7/bootstrap",
    "bt3.data-tables": "resources/js/libs/bt3/data-tables/2.1.1/bt3.data-tables",
    "bt3.hover-dropdown": "resources/js/libs/bt3/hover-dropdown/2.2.1/bt3.hover-dropdown",
    "bt4": "resources/js/libs/bt4/4.0.0/bootstrap",
    "centrifuge": "resources/js/libs/centrifuge/1.4.8/centrifuge",
    "clipboard": "resources/js/libs/clipboard/1.7.1/clipboard.min",
    "datatables.net": "resources/js/libs/jquery/data-tables/1.10.16/jquery.data-tables",
    "global": "resources/js/global",
    "highlight": "resources/js/libs/highlight/9.12.0/highlight",
    "jquery": "resources/js/libs/jquery/2.2.4/jquery.min",
    "jquery.form": "resources/js/libs/jquery/form/4.2.2/jquery.form",
    "jquery.layer": "resources/js/libs/jquery/layer/3.1.1/jquery.layer",
    "jquery.metis-menu": "resources/js/libs/jquery/metis-menu/2.7.2/jquery.metis-menu",
    "jquery.slimscroll": "resources/js/libs/jquery/slimscroll/1.3.8/jquery.slimscroll.min",
    "jquery.toastr": "resources/js/libs/jquery/toastr/2.1.3/jquery.toastr",
    "jquery.tokenize2": "resources/js/libs/jquery/tokenize2//jquery.tokenize2",
    "jquery.validation": "resources/js/libs/jquery/validation/1.17.0/jquery.validation",
    "jquery.webuploader": "resources/js/libs/jquery/webuploader/0.1.5/jquery.webuploader",
    "js-cookie": "resources/js/libs/js-cookie/2.2.0/js-cookie",
    "pace": "resources/js/libs/pace/1.0.2/pace.min",
    "requirejs": "resources/js/libs/requirejs/require",
    "smooth-scroll": "resources/js/libs/smooth-scroll/1.4.0/smooth-scroll",
    "sockjs": "resources/js/libs/sockjs/0.3.4/sockjs",
    "underscore": "resources/js/libs/underscore/1.8.3/underscore",
    "vkbeautify": "resources/js/libs/vkbeautify/vkbeautify",
    "wow": "resources/js/libs/wow/1.1.2/wow.min"
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
    },
    "wow": {
        "exports": "WOW"
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
/**
 * requirejs 配置文件
 * - 排序方式 文件夹的排序方式
 */
var alias = {
	/* Start Common Plugin  ----------- */
	'alloyimage'                       : 'libs/alloyimage/1.1.0/alloyimage',
	'autosize'                         : 'libs/autosize/3.0.15/autosize.min',
	'backbone'                         : 'libs/backbone/1.2.3/backbone-min',
	'cal-heatmap'                      : 'libs/cal-heatmap/3.6.2/cal-heatmap.min',
	'calendar-heatmap'                 : 'libs/calender-heatmap/calendar-heatmap',
	'clipboard'                        : 'libs/clipboard/1.5.16/clipboard.min',
	'codemirror'                       : 'libs/codemirror/5.16.0/lib/codemirror',
	'codemirror.inline-attachment'     : 'libs/codemirror/inline-attachment/inline-attachment',
	'd3'                               : 'libs/d3/4.3.0/d3.v4.min',
	'echarts'                          : 'libs/echarts/3.3.1/echarts.min',
	'editormd'                         : 'libs/editormd/1.5.0/editormd.amd',
	'editormd.link-dialog'             : "libs/editormd/1.5.0/plugins/link-dialog/link-dialog",
	'editormd.reference-link-dialog'   : "libs/editormd/1.5.0/plugins/reference-link-dialog/reference-link-dialog",
	'editormd.image-dialog'            : "libs/editormd/1.5.0/plugins/image-dialog/image-dialog",
	'editormd.code-block-dialog'       : "libs/editormd/1.5.0/plugins/code-block-dialog/code-block-dialog",
	'editormd.table-dialog'            : "libs/editormd/1.5.0/plugins/table-dialog/table-dialog",
	'editormd.emoji-dialog'            : "libs/editormd/1.5.0/plugins/emoji-dialog/emoji-dialog",
	'editormd.goto-line-dialog'        : "libs/editormd/1.5.0/plugins/goto-line-dialog/goto-line-dialog",
	'editormd.help-dialog'             : "libs/editormd/1.5.0/plugins/help-dialog/help-dialog",
	'editormd.html-entities-dialog'    : "libs/editormd/1.5.0/plugins/html-entities-dialog/html-entities-dialog",
	'editormd.preformatted-text-dialog': "libs/editormd/1.5.0/plugins/preformatted-text-dialog/preformatted-text-dialog",
	'emojify'                          : 'libs/emojify/1.1.0/emojify.min',
	'excanvas'                         : 'libs/excanvas/excanvas.min',
	'flowchart'                        : 'libs/flowchart/flowchart.min',
	'flowplayer'                       : 'libs/flowplayer/6.0.5/flowplayer',
	'fuelux'                           : 'libs/fuelux/3.15.4/fuelux.min',
	'full-avatar-editor'               : 'libs/full-avatar-editor/2.3/fullAvatarEditor',
	'fullcalendar'                     : 'libs/fullcalendar/2.9.0/fullcalendar.min',
	'h5shiv'                           : 'libs/h5shiv/3.7.2/html5shiv.min',
	'handlebars'                       : 'libs/handlebars/1.1.2/handlebars',
	'highlight'                        : 'libs/highlight/8.1/highlight',
	'holder'                           : 'libs/holder/2.3.2/holder',
	'i18next'                          : 'libs/i18next/i18next.min',
	'inline-attachment'                : 'libs/inline-attachment/2.0.3/inline-attachment',
	'input.inline-attachment'          : 'libs/inline-attachment/2.0.3/input.inline-attachment',
	'js-cookie'                        : 'libs/js-cookie/2.1.2/js-cookie',
	'js-storage'                       : 'libs/js-storage/1.0.2/js.storage.min',
	'json'                             : 'libs/json/1.0.2/json2',
	'katex'                            : 'libs/katex/0.6.0/katex.min',
	'ke'                               : 'libs/kindeditor/4.1.11/kindeditor-all',
	'ke-zh_CN'                         : 'libs/kindeditor/4.1.7/lang/zh_CN.js',
	'livecss'                          : 'libs/livecss/livecss',
	'marked'                           : 'libs/marked/0.3.4/marked.min',
	'md5'                              : 'libs/md5/2.2/md5',
	'modernizr'                        : 'libs/modernizr/2.6.2/modernizr.min',
	'moment'                           : 'libs/moment/2.10.6/moment_locales',
	'moment-range'                     : 'libs/moment-range/2.2.0/moment-range.min',
	'mootools'                         : 'libs/mootools/1.6.0/MooTools-Core',
	'morris'                           : 'libs/morris/0.5.1/morris',
	'mustache'                         : 'libs/mustache/2.2.1/mustache',
	'pace'                             : 'libs/pace/1.0.2/pace.min',
	'parsley'                          : 'libs/parsley/2.6.5/parsley.min',
	'plupload'                         : 'libs/plupload/2.1.9/plupload.full.min',
	'prettify'                         : 'libs/prettify/prettify.min',
	'prism'                            : 'libs/prism/1.5.1/prism',
	'raphael'                          : 'libs/raphael/2.2.1/raphael.min',
	'respond'                          : 'libs/respond/1.4.2/respond.min',
	'sequence-diagram'                 : 'libs/sequence-diagram/1.0.6/sequence-diagram.min',
	'smooth-scroll'                    : 'libs/smooth-scroll/9.1.4/smooth-scroll.min',
	'swfobject'                        : 'libs/swfobject/2.3.0/swfobject',
	'underscore'                       : 'libs/underscore/1.8.3/underscore-min',
	'vue'                              : 'libs/vue/1.0.26/vue.min',
	'vue.resource'                     : 'libs/vue/vue-resource/0.9.3/vue-resource.min',
	'wow'                              : 'libs/wow/1.1.2/wow.min',
	'wysihtml5'                        : 'libs/wysihtml5/0.3.0/wysihtml5.min',
	'wysihtml5.advanced'               : 'libs/wysihtml5/0.3.0/advanced',
	'zero-clipboard'                   : 'libs/zero-clipboard/2.3.0-bata1/ZeroClipboard.min',
	/* End Common Plugin    ----------- */

	/* Start jQuery     ----------- */
	'jquery'                         : 'libs/jquery/1.12.0/jquery.min',
	'$2'                             : 'libs/jquery/2.2.4/jquery',
	'$3'                             : 'libs/jquery/3.1.0/jquery.min',
	'jquery.art-dialog'              : 'libs/jquery/art-dialog/6.0.0/dialog-plus',
	'jquery.atwho'                   : 'libs/jquery/atwho/1.5.1/jquery.atwho.min',
	'jquery.backstretch'             : 'libs/jquery/backstretch/2.0.4/jquery.backstretch.min',
	'jquery.bg-pos'                  : 'libs/jquery/bg-pos/1.0.2/jquery.bgPos',
	'jquery.bg-stretcher'            : 'libs/jquery/bg-stretcher/3.3.0/jquery.bgstretcher.min',
	'jquery.bgiframe'                : 'libs/jquery/bgiframe/2.1.3/jquery.bgiframe',
	'jquery.blockui'                 : 'libs/jquery/blockui/2.70.0/jquery.blockui.min',
	'jquery.bootpag'                 : 'libs/jquery/bootpag/jquery.bootpag.min',
	'jquery.browser'                 : 'libs/jquery/browser/jquery.browser',
	'jquery.caret'                   : 'libs/jquery/caret/jquery.caret.min',
	'jquery.chained'                 : 'libs/jquery/chained/1.0/jquery.chained',
	'jquery.chained-remote'          : 'libs/jquery/chained/1.0/jquery.chained.remote',
	'jquery.counter-up'              : 'libs/jquery/counter-up/1.0/jquery.counterup.min',
	'jquery.data-tables'             : 'libs/jquery/data-tables/1.10.11/jquery.dataTables.min',
	'jquery.data-tables.bt3'         : 'libs/jquery/data-tables/1.10.11/dataTables.bootstrap.min',
	'jquery.date-range-picker'       : 'libs/jquery/date-range-picker/0.9.0/jquery.daterangepicker.min',
	'jquery.datetimepicker'          : 'libs/jquery/datetimepicker/2.5.9/jquery.datetimepicker.full.min',
	'jquery.easing'                  : 'libs/jquery/easing/1.4.0/jquery.easing.min',
	'jquery.easypiechart'            : 'libs/jquery/easypiechart/jquery.easypiechart.min',
	'jquery.editable'                : 'libs/jquery/editable/1.7.1/jquery.editable',
	'jquery.fancybox'                : 'libs/jquery/fancybox/2.1.5/jquery.fancybox',
	'jquery.file-upload'             : 'libs/jquery/file-upload/js/jquery.fileupload',
	'jquery.flexslider'              : 'libs/jquery/flexslider/2.1.0/jquery.flexslider',
	'jquery.flowchart'               : 'libs/jquery/flowchart/jquery.flowchart.min',
	'jquery.form'                    : 'libs/jquery/form/3.51.0/jquery.form',
	'jquery.gantt'                   : 'libs/jquery/gantt/jquery.fn.gantt.min',
	'jquery.gritter'                 : 'libs/jquery/gritter/1.7.4/jquery.gritter',
	'jquery.history'                 : 'libs/jquery/history/jquery.history',
	'jquery.hover3d'                 : 'libs/jquery/hover3d/jquery.hover3d.min',
	'jquery.icheck'                  : 'libs/jquery/icheck/1.0.2/jquery.icheck',
	'jquery.idle-timer'              : 'libs/jquery/idle-timeout/jquery.idletimer',
	'jquery.idle-timeout'            : 'libs/jquery/idle-timeout/jquery.idletimeout',
	'jquery.inline-attachment'       : 'libs/inline-attachment/2.0.1/jquery.inline-attachment',
	'jquery.input-ip-address-control': 'libs/jquery/input-ip-address-control/v0.1beta/jquery.input-ip-address-control-1.0.min',
	'jquery.inputmask'               : 'libs/jquery/inputmask/3.3.2/jquery.inputmask.bundle.min',
	'jquery.j-diaporama'             : 'libs/jquery/j-diaporama/2.0.0/jquery.jDiaporama',
	'jquery.jstree'                  : 'libs/jquery/jstree/dist/jstree.min',
	'jquery.kinslideshow'            : 'libs/jquery/kinslideshow/1.2.1/kinslideshow',
	'jquery.knob'                    : 'libs/jquery/knob/js/jquery.knob',
	'jquery.layer'                   : 'libs/jquery/layer/3.0.1/layer',
	'jquery.lazyload'                : 'libs/jquery/lazyload/1.9.3/jquery.lazyload',
	'jquery.linkage-sel'             : 'libs/jquery/linkage-sel/2.4/LinkageSel',
	'jquery.live-search'             : 'libs/jquery/live-search/1.0/jquery.liveSearch',
	'jquery.marquee-slide'           : 'libs/jquery/marquee-slide/jquery.marquee-slide',
	'jquery.menu-aim'                : 'libs/jquery/menu-aim/jquery.menu-aim',
	'jquery.metis-menu'              : 'libs/jquery/metis-menu/2.5.2/metisMenu',
	'jquery.migrate'                 : 'libs/jquery/migrate/1.2.1/jquery-migrate.min',
	'jquery.minicolors'              : 'libs/jquery/minicolors/jquery.minicolors.min',
	'jquery.mixitup'                 : 'libs/jquery/mixitup/jquery.mixitup',
	'jquery.mockjax'                 : 'libs/jquery/mockjax/1.5.0/jquery.mockjax',
	'jquery.mousewheel'              : 'libs/jquery/mousewheel/jquery.mousewheel.min',
	'jquery.multi-select'            : 'libs/jquery/multi-select/js/jquery.multi-select',
	'jquery.nestable'                : 'libs/jquery/nestable/jquery.nestable',
	'jquery.notific8'                : 'libs/jquery/notific8/jquery.notific8',
	'jquery.parallax'                : 'libs/jquery/parallax/1.1.3/jquery.parallax',
	'jquery.popr'                    : 'libs/jquery/popr/1.0/popr.min',
	'jquery.poshytip'                : 'libs/jquery/poshytip/jquery.poshytip.min',
	'jquery.pulsate'                 : 'libs/jquery/pulsate/jquery.pulsate.min',
	'jquery.raty'                    : 'libs/jquery/raty/2.7.0/jquery.raty',
	'jquery.scroll-to'               : 'libs/jquery/scroll-to/2.1.3/jquery.scrollTo.min',
	'jquery.share'                   : 'libs/jquery/share/jquery.share.min',
	'jquery.slider'                  : 'libs/jquery/slider/21.1.5/jssor.slider.min',
	'jquery.slides'                  : 'libs/jquery/slides/jquery.slides.min',
	'jquery.slim-scroll'             : 'libs/jquery/slim-scroll/1.3.8/jquery.slimscroll',
	'jquery.sparkline'               : 'libs/jquery/sparkline/2.1.2/jquery.sparkline.min',
	'jquery.spectrum'                : 'libs/jquery/spectrum/1.7.0/jquery.spectrum',
	'jquery.sumo-select'             : 'libs/jquery/sumo-select/3.0.0/jquery.sumoselect',
	'jquery.supersized'              : 'libs/jquery/supersized/3.2.7/supersized',
	'jquery.superslide'              : 'libs/jquery/superslide/2.1/jquery.superslide',
	'jquery.swiper'                  : 'libs/jquery/swiper/3.3.1/swiper.jquery.min',
	'jquery.tagging-js'              : 'libs/jquery/tagging-js/1.3.3/jquery.taggingJS',
	'jquery.text-search'             : 'libs/jquery/text-search/1.0/jquery.textSearch-1.0',
	'jquery.think-keyboard'          : 'libs/jquery/think-keyboard/jquery.thinkkeyboard',
	'jquery.tinycarousel'            : 'libs/jquery/tinycarousel/2.1.6/jquery.tinycarousel',
	'jquery.toastr'                  : 'libs/jquery/toastr/toastr',
	'jquery.tocify'                  : 'libs/jquery/tocify/1.9.0/jquery.tocify',
	'jquery.tokenize'                : 'libs/jquery/tokenize/2.5.2/jquery.tokenize',
	'jquery.tools'                   : 'libs/jquery/tools/1.2.7/jquery.tools',
	'jquery.tree-select'             : 'libs/jquery/tree-select/2014.08.29/jquery.treeselect.min',
	'jquery.treetable'               : 'libs/jquery/treetable/3.2.0/jquery.treeTable',
	'jquery.ui'                      : 'libs/jquery/ui/1.11.2.custom/jquery-ui.min',
	'jquery.uniform'                 : 'libs/jquery/uniform/jquery.uniform.min',
	'jquery.uploadify'               : 'libs/jquery/uploadify/3.2.1/jquery.uploadify',
	'jquery.validate'                : 'libs/jquery/validate/1.13.0/jquery.validate',
	'jquery.vmap'                    : 'libs/jquery/vmap/1.5.0/jquery.vmap',
	'jquery.vmap.world'              : 'libs/jquery/vmap/1.5.0/jquery.vmap.world',
	'jquery.waypoints'               : 'libs/jquery/waypoints/jquery.waypoints.min',
	'jquery.webuploader'             : 'libs/jquery/webuploader/0.1.5/webuploader.min',
	'jquery.zebra-datepicker'        : 'libs/jquery/zebra-datepicker/1.9.5/zebra_datepicker',
	'jquery.zebra-dialog'            : 'libs/jquery/zebra-dialog/1.3.12/zebra_dialog.src',
	'jquery.ztree'                   : 'libs/jquery/ztree/3.5/jquery.ztree.min',
	/* End jQuery       ----------- */

	/* Start Bootstrap 3  ----------- */
	'bt3'                : 'libs/jquery.bt3/3.3.7/bootstrap',
	'bt3.colorpicker'    : 'libs/jquery.bt3/colorpicker/bootstrap-colorpicker',
	'bt3.confirmation'   : 'libs/jquery.bt3/confirmation/bootstrap-confirmation.min',
	'bt3.contextmenu'    : 'libs/jquery.bt3/contextmenu/bootstrap-contextmenu',
	'bt3.data-tables'    : 'libs/jquery.bt3/data-tables/dataTables.bootstrap.min',
	'bt3.datepaginator'  : 'libs/jquery.bt3/datepaginator/bootstrap-datepaginator.min',
	'bt3.datepicker'     : 'libs/jquery.bt3/datepicker/bootstrap-datepicker.min',
	'bt3.daterangepicker': 'libs/jquery.bt3/daterangepicker/daterangepicker',
	'bt3.datetimepicker' : 'libs/jquery.bt3/datetimepicker/bootstrap-datetimepicker.min',
	'bt3.fileinput'      : 'libs/jquery.bt3/fileinput/bootstrap-fileinput',
	'bt3.growl'          : 'libs/jquery.bt3/growl/jquery.bootstrap-growl.min',
	'bt3.hover-dropdown' : 'libs/jquery.bt3/hover-dropdown/hover-dropdown.min',
	'bt3.markdown'       : 'libs/jquery.bt3/markdown/bootstrap-markdown',
	'bt3.maxlength'      : 'libs/jquery.bt3/maxlength/bootstrap-maxlength.min',
	'bt3.modal'          : 'libs/jquery.bt3/modal/bootstrap-modal',
	'bt3.modalmanager'   : 'libs/jquery.bt3/modal/bootstrap-modalmanager',
	'bt3.pwstrength'     : 'libs/jquery.bt3/pwstrength/2.0.4/pwstrength',
	'bt3.popover-x'      : 'libs/jquery.bt3/popover-x/1.4.3/bootstrap-popover-x.min',
	'bt3.select'         : 'libs/jquery.bt3/select/bootstrap-select.min',
	'bt3.selectsplitter' : 'libs/jquery.bt3/selectsplitter/bootstrap-selectsplitter.min',
	'bt3.sessiontimeout' : 'libs/jquery.bt3/sessiontimeout/bootstrap-session-timeout.min',
	'bt3.summernote'     : 'libs/jquery.bt3/summernote/summernote.min',
	'bt3.switch'         : 'libs/jquery.bt3/switch/bootstrap-switch.min',
	'bt3.tabdrop'        : 'libs/jquery.bt3/tabdrop/bootstrap-tabdrop',
	'bt3.tagsinput'      : 'libs/jquery.bt3/tagsinput/bootstrap-tagsinput.min',
	'bt3.timepicker'     : 'libs/jquery.bt3/timepicker/bootstrap-timepicker.min',
	'bt3.touchspin'      : 'libs/jquery.bt3/touchspin/bootstrap.touchspin.min',
	'bt3.twbs-pagination': 'libs/jquery.bt3/twbs-pagination/1.4.1/jquery.twbs-pagination',
	'bt3.typeahead'      : 'libs/jquery.bt3/typeahead/bootstrap3-typeahead.min',
	'bt3.wizard'         : 'libs/jquery.bt3/wizard/jquery.bootstrap.wizard.min',
	'bt3.wysihtml5'      : 'libs/jquery.bt3/wysihtml5/bootstrap-wysihtml5',
	'bt3.x-editable'     : 'libs/jquery.bt3/x-editable/bootstrap-editable.min'
	/* End Bootstrap 3    ----------- */

	/* Start Project  ----------- */

	/* End Project    ----------- */

};

var shim = {
	// start common plugin
	'alloyimage'                  : {exports: 'AlloyImage'},
	'cal-heatmap'                 : {
		exports: 'CalHeatMap',
		deps   : ['d3']
	},
	'calendar-heatmap'            : {
		exports: 'calendarHeatmap',
		deps   : ['d3', 'moment']
	},
	'codemirror.inline-attachment': {exports: 'inlineAttachment'},
	'emojify'                     : {exports: 'emojify'},
	'fuelux'                      : ['jquery', 'bt3'],
	'handlebars'                  : {exports: 'Handlebars'},
	'highlight'                   : {
		exports: 'hljs'
	},
	'i18next'                     : {exports: 'i18next'},
	'inline-attachment'           : {
		exports: 'inlineAttachment'
	},
	'input.inline-attachment'     : {
		exports: 'attachToInput',
		deps   : ['inline-attachment']
	},
	'raphael'                     : {exports: 'Raphael'},
	'js-cookie'                   : {exports: 'Cookies'},
	'js-storage'                  : {exports: 'Storages'},
	'json'                        : {exports: 'JSON'},
	'ke'                          : {exports: 'KindEditor'},
	'marked'                      : {exports: 'marked'},
	'moment'                      : {exports: 'moment'},
	'moment-range'                : ['moment'],
	'morris'                      : {
		exports: 'Morris',
		deps   : ['jquery', 'raphael']
	},
	'plupload'                    : {
		exports: 'plupload'
	},
	'sequence-diagram'            : {
		exports: 'Diagram',
		deps   : ['jquery', 'raphael', 'underscore']
	},
	'vue'                         : {
		exports: 'Vue'
	},
	'wow'                         : {
		exports: 'WOW'
	},
	'wysihtml5'                   : {
		exports: 'wysihtml5',
		deps   : ['wysihtml5.advanced']
	},
	'zero-clipboard'              : {exports: 'ZeroClipboard'},
	// end common plugin

	// start jQuery Plugin
	'$3'                             : {exports: 'jQuery'},
	'jquery'                         : {exports: 'jQuery'},
	'jquery.art-dialog'              : ['jquery'],
	'jquery.atwho'                   : ['jquery', 'jquery.caret'],
	'jquery.backstretch'             : ['jquery'],
	'jquery.bgiframe'                : ['jquery'],
	'jquery.bg-pos'                  : ['jquery'],
	'jquery.bg-stretcher'            : ['jquery'],
	'jquery.blockui'                 : ['jquery'],
	'jquery.bootpag'                 : ['jquery'],
	'jquery.browser'                 : ['jquery'],
	'jquery.caret'                   : ['jquery'],
	'jquery.chained'                 : ['jquery'],
	'jquery.counter-up'              : ['jquery', 'jquery.waypoints'],
	'jquery.date-range-picker'       : ['moment', 'jquery'],
	'jquery.datetimepicker'          : ['jquery', 'jquery.mousewheel'],
	'jquery.data-tables'             : ['jquery'],
	'jquery.easing'                  : ['jquery'],
	'jquery.easypiechart'            : ['jquery'],
	'jquery.editable'                : ['jquery'],
	'jquery.fancybox'                : ['jquery'],
	'jquery.file-upload'             : ['jquery'],
	'jquery.flexslider'              : ['jquery'],
	'jquery.form'                    : ['jquery'],
	'jquery.flowchart'               : ['jquery.ui'],
	'jquery.gantt'                   : ['jquery'],
	'jquery.gritter'                 : ['jquery'],
	'jquery.history'                 : ['jquery'],
	'jquery.icheck'                  : ['jquery'],
	'jquery.idle-timer'              : ['jquery'],
	'jquery.idle-timeout'            : ['jquery', 'jquery.idle-timer'],
	'jquery.inline-attachment'       : ['jquery'],
	'jquery.input-ip-address-control': ['jquery'],
	'jquery.inputmask'               : ['jquery'],
	'jquery.j-diaporama'             : ['jquery'],
	'jquery.jstree'                  : ['jquery'],
	'jquery.kinslideshow'            : ['jquery'],
	'jquery.knob'                    : ['jquery'],
	'jquery.layer'                   : {
		exports: 'layer',
		deps   : ['jquery']
	},
	'jquery.lazyload'                : ['jquery'],
	'jquery.linkage-sel'             : {
		exports: 'LinkageSel',
		deps   : ['jquery']
	},
	'jquery.marquee-slide'           : ['jquery'],
	'jquery.menu-aim'                : ['jquery'],
	'jquery.metis-menu'              : ['jquery'],
	'jquery.migrate'                 : ['jquery'],
	'jquery.minicolors'              : ['jquery'],
	'jquery.mixitup'                 : ['jquery'],
	'jquery.mockjax'                 : ['jquery'],
	'jquery.mousewheel'              : ['jquery'],
	'jquery.multi-select'            : ['jquery'],
	'jquery.nestable'                : ['jquery'],
	'jquery.notific8'                : ['jquery'],
	'jquery.poshytip'                : ['jquery'],
	'jquery.parallax'                : ['jquery'],
	'jquery.pulsate'                 : ['jquery'],
	'jquery.popr'                    : ['jquery'],
	'jquery.raty'                    : ['jquery'],
	'jquery.scroll-to'               : ['jquery'],
	'jquery.share'                   : ['jquery'],
	'jquery.slider'                  : ['jquery'],
	'jquery.slides'                  : ['jquery'],
	'jquery.slim-scroll'             : ['jquery'],
	'jquery.sparkline'               : ['jquery'],
	'jquery.spectrum'                : ['jquery'],
	'jquery.sumo-select'             : ['jquery'],
	'jquery.supersized'              : ['jquery'],
	'jquery.superslide'              : ['jquery'],
	'jquery.swiper'                  : ['jquery'],
	'jquery.tagging-js'              : ['jquery'],
	'jquery.text-search'             : ['jquery'],
	'jquery.tinycarousel'            : ['jquery'],
	'jquery.toastr'                  : ['jquery'],
	'jquery.tocify'                  : ['jquery'],
	'jquery.tokenize'                : ['jquery'],
	'jquery.tools'                   : ['jquery'],
	'jquery.tree-select'             : ['jquery'],
	'jquery.treetable'               : ['jquery'],
	'jquery.ui'                      : ['jquery'],
	'jquery.uniform'                 : ['jquery'],
	'jquery.uploadify'               : ['jquery'],
	'jquery.validate'                : ['jquery'],
	'jquery.vmap'                    : ['jquery'],
	'jquery.vmap.world'              : ['jquery'],
	'jquery.webuploader'             : {
		exports: 'WebUploader',
		deps   : ['jquery']
	},
	'jquery.waypoints'               : {
		exports: 'Waypoints',
		deps   : ['jquery']
	},
	'jquery.zebra-datepicker'        : ['jquery'],
	'jquery.zebra-dialog'            : ['jquery'],
	'jquery.ztree'                   : ['jquery'],
	// end jQuery Plugin

	// start jQuery Bootstrap
	'bt3'                : ['jquery'],
	'bt3.colorpicker'    : ['jquery', 'bt3'],
	'bt3.confirmation'   : ['jquery', 'bt3'],
	'bt3.contextmenu'    : ['jquery', 'bt3'],
	'bt3.data-tables'    : ['jquery', 'bt3'],
	'bt3.datepaginator'  : ['moment', 'jquery', 'bt3', 'bt3.datepicker'],
	'bt3.datepicker'     : ['jquery', 'bt3'],
	'bt3.daterangepicker': ['moment', 'jquery', 'bt3'],
	'bt3.datetimepicker' : ['moment', 'jquery', 'bt3'],
	'bt3.fileinput'      : ['jquery', 'bt3'],
	'bt3.growl'          : ['jquery', 'bt3'],
	'bt3.hover-dropdown' : ['jquery', 'bt3'],
	'bt3.markdown'       : ['jquery', 'bt3'],
	'bt3.maxlength'      : ['jquery', 'bt3'],
	'bt3.modal'          : ['bt3.modalmanager'],
	'bt3.modalmanager'   : ['jquery', 'bt3'],
	'bt3.pwstrength'     : ['jquery', 'bt3'],
	'bt3.popover-x'      : ['jquery', 'bt3'],
	'bt3.select'         : ['jquery', 'bt3'],
	'bt3.selectsplitter' : ['jquery', 'bt3'],
	'bt3.sessiontimeout' : ['jquery', 'bt3'],
	'bt3.summernote'     : ['jquery', 'bt3'],
	'bt3.switch'         : ['jquery', 'bt3'],
	'bt3.tabdrop'        : ['jquery', 'bt3'],
	'bt3.tagsinput'      : ['jquery', 'bt3'],
	'bt3.timepicker'     : ['jquery', 'bt3'],
	'bt3.touchspin'      : ['jquery', 'bt3'],
	'bt3.twbs-pagination': ['jquery', 'bt3'],
	'bt3.typeahead'      : ['jquery', 'bt3'],
	'bt3.wizard'         : ['jquery', 'bt3'],
	'bt3.wysihtml5'      : ['wysihtml5', 'jquery', 'bt3'],
	'bt3.x-editable'     : ['jquery', 'bt3']
	// end jQuery Bootstrap
};

//noinspection JSUnresolvedVariable
var browser = {
	versions: function () {
		var u   = navigator.userAgent,
		    app = navigator.appVersion;
		return {
			trident: u.indexOf('Trident') > -1, //IE内核
			presto : u.indexOf('Presto') > -1, //opera内核
			webKit : u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
			gecko  : u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核
			mobile : !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
			ios    : !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
			android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
			iPhone : u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
			iPad   : u.indexOf('iPad') > -1, //是否iPad
			webApp : u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
			weixin : u.indexOf('MicroMessenger') > -1, //是否微信 （2015-01-22新增）
			qq     : u.match(/\sQQ/i) == " qq" //是否QQ
		};
	}(),
	language: (navigator.browserLanguage || navigator.language).toLowerCase()
};
if (browser.versions.mobile) {
	alias.jquery = alias.$3;
}

// appends for single project
// noinspection JSUnresolvedVariable
if (typeof appends != 'undefined' && typeof appends == 'object') {
	for (var k in appends) {
		alias[k] = appends[k];
	}
}

// require js config
requirejs.config({
	baseUrl: '/assets/js',
	paths  : alias,
	shim   : shim
});



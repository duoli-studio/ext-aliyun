module.exports = {
	root          : true,
	parser        : 'babel-eslint',
	parserOptions : {
		sourceType : 'module',
	},
	env           : {
		browser : true,
	},
	extends       : 'airbnb-base',
	plugins       : [
		'html',
	],
	settings      : {
		'import/resolver' : {
			webpack : {
				config : 'build/webpack.base.conf.js',
			},
		},
	},
	rules         : {
		// allow paren-less arrow functions
		'arrow-parens'                      : 0,
		'eol-last'                          : 0,
		'guard-for-in'                      : 0,
		'import/extensions'                 : ['error', 'always', {
			js  : 'never',
			vue : 'never',
		}],
		'import/no-extraneous-dependencies' : ['error', {
			optionalDependencies : ['test/unit/index.js'],
		}],
		'no-console'                        : process.env.NODE_ENV === 'production' ? 2 : 0,
		'no-debugger'                       : process.env.NODE_ENV === 'production' ? 2 : 0,
		'no-param-reassign'                 : ['error', {
			props : false,
		}],
		'no-plusplus'                       : ['error', {
			allowForLoopAfterthoughts : true,
		}],
		'no-restricted-syntax'              : ['error', 'WithStatement'],
		'no-underscore-dangle'              : 0,
		'object-curly-spacing'              : [0],
		"disallowTabs"                      : false,

		// close indent
		'indent'                      : 0,
		'key-spacing'                 : [
			"error",
			{
				"align"       : "colon",
				"afterColon"  : true,
				"beforeColon" : true,
				"mode"        : "minimum"
			}
		],
		"space-before-function-paren" : [
			"error",
			"never"
		],
		"no-tabs"                     : "off",
		"brace-style"                 : [
			"error",
			"stroustrup",
			{
				"allowSingleLine" : true
			}
		],
		"object-curly-newline"        : ["error", {
			"consistent"  : true,
			minProperties : 4
		}],
		// allow async-await
		'generator-star-spacing'      : 0,
		"no-mixed-spaces-and-tabs"    : 0,
		"comma-dangle"                : ["error", "only-multiline"]
	},
};
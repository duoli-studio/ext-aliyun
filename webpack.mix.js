/*
 * Copyright (C) 2013-2017 Shandong Liexiang Tec, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

var mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.sass(
	'resources/assets/scss/basic.scss',
	'public/resources/css'
).sass(
	'resources/assets/scss/slt.scss',
	'public/resources/css'
).sass(
	'resources/assets/scss/landing.scss',
	'public/resources/css'
).sass(
	'modules/system/resources/scss/develop/style.scss',
	'public/resources/css/develop.css'
).sass(
	'modules/system/resources/scss/backend/style.scss',
	'public/resources/css/backend.css'
);
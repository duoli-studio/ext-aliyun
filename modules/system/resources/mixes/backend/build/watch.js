require('./check-versions')();

process.env.NODE_ENV = 'production';

let buildWebpackConfig = require('./webpack.prod.conf');
let chalk = require('chalk');
let config = require('../config');
let path = require('path');
let rm = require('rimraf');
let merge = require('webpack-merge');
let shell = require('shelljs');
let webpack = require('webpack');
let webpackConfig = merge(buildWebpackConfig, {
    plugins: [
        new webpack.ProgressPlugin()
    ],
    watch: true
});

rm(path.join(config.build.assetsRoot, config.build.assetsSubDirectory), err => {
    if (err) throw err;
    webpack(webpackConfig, function (err, stats) {
        if (err) throw err;
        console.log('\n');
        process.stdout.write(stats.toString({
                colors: true,
                modules: true,
                children: false,
                chunks: false,
                chunkModules: false
            }) + '\n');
        let assetsPath = path.join(__dirname, '../../../../../../public/assets/backend');

        console.log(chalk.cyan('  Moving Dist files to path ' + assetsPath + '\n'));

        shell.rm('-rf', assetsPath);
        shell.mkdir('-p', assetsPath);
        shell.config.silent = true;
        shell.cp('-R', path.join(__dirname, '../dist/assets/backend/css'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/backend/fonts'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/backend/images'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/backend/js'), assetsPath);
        shell.config.silent = false;

        console.log(chalk.cyan(`  Build completed at ${(new Date()).toLocaleString()}.`));
        console.log(chalk.cyan('  Watching ...\n'));
    });
});
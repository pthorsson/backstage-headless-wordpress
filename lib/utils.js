const chalk = require('chalk');
const fs = require('fs');
const fse = require('fs-extra');
const fetch = require('node-fetch');
const rimraf = require('rimraf');
const path = require('path');

const config = require('./config');

const wpBasePath = path.join(config.rootDir, 'wordpress-base');
const wpPath = path.join(config.rootDir, 'wordpress');
const wpCliPhar = path.join(config.rootDir, 'wp-cli.phar');

let utils = {

    /**
     * Logging function
     */
    log(message, options) {
        options = options || {};
        options.type = options.type && /(error|warning|success|blank)/.test(options.type) ? options.type : 'info';
        options.top = typeof options.top === 'number' && options.top > 0 ? options.top : 0;
        options.bottom = typeof options.bottom === 'number' && options.bottom > 0 ? options.bottom : 0;

        const pad = (n, c) => {
            let str = '';
            for (let i = 0; i < n; i++) str += c;
            return str;
        };

        let prefix = '[Backstage]';

        let color = {
            error: 'red',
            warning: 'yellow',
            success: 'green',
            info: 'blue'
        };

        console.log(`${pad(options.top, '\n')}${options.type === 'blank' ? pad(prefix.length, ' ') : chalk[color[options.type]](prefix)} ${message}${pad(options.bottom, '\n')}`);
    },

    tasks: {

        /**
         * Installer task - Download wp-cli
         */
        downloadWpCli: () => (next, exit) => {
            utils.log('Downloading wp-cli ...', { top: 1 });

            fetch('https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar')
                .then(res => {
                    const file = fs.createWriteStream(wpCliPhar);
                    res.body.pipe(file);
                    file.on('finish', () => next());
                });
        },

        /**
         * Installer task - Delete previous installation
         */
        deletePreviousInstallation: () => (next, exit) => {
            utils.log('Removing previous WordPress installation ...');

            rimraf(wpPath, () => {
                next();
            });
        },

        /**
         * Installer task - Clone WordPress base
         */
        cloneBase: () => (next, exit) => {
            utils.log('Cloning WordPress base ...');

            fse.copy(wpBasePath, wpPath, err => {
                if (err) return exit(err);
                next();
            });
        }
    }
};

module.exports = utils;

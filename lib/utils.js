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
    log(message, type) {
        type = type && /(error|warning|success|blank)/.test(type) ? type : 'info';

        let color = {
            error: 'red',
            warning: 'yellow',
            success: 'green',
            info: 'blue'
        };

        console.log(`${type === 'blank' ? '      ' : chalk[color[type]]('[HLWP]')} ${message}`);
    },

    tasks: {
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
         * Installer task - Download wp-cli
         */
        downloadWpCli: () => (next, exit) => {
            utils.log('Downloading wp-cli ...');

            fetch('https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar')
                .then(res => {
                    const file = fs.createWriteStream(wpCliPhar);
                    res.body.pipe(file);
                    file.on('finish', () => next());
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

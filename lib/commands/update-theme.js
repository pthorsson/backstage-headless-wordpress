const path = require('path');
const fs = require('fs');
const fse = require('fs-extra');
const rimraf = require('rimraf');

const utils = require('../utils');
const wp = require('../wp');
const config = require('../config');

const wpBasePath = path.join(config.rootDir, 'wordpress-base');
const wpPath = path.join(config.rootDir, 'wordpress');

const removeLiveTheme = liveTheme => new Promise((resolve, reject) => {
    let keep = ['acf-json', 'backstage-json'],
        files = fs.readdirSync(liveTheme);

    _removeFile();

    function _removeFile() {
        if (!files.length) {
            resolve();
            return;
        }

        let file = files.shift();

        rimraf(liveTheme, err => {
            if (err) return reject(err);
            _removeFile()
        });
    }
});

const copyBaseTheme = (src, dest) => new Promise((resolve, reject) => {
    fse.copy(src, dest, err => {
        if (err) return reject(err);
        resolve();
    });
});

module.exports = themeName => {
    let baseTheme = path.join(wpBasePath, 'wp-content/themes', themeName),
        liveTheme = path.join(wpPath, 'wp-content/themes', themeName);

    if (fs.existsSync(baseTheme)) {
        utils.log(`Updating plugin "${themeName}" ...`, { top: 1 });
        utils.log(`Removing current version ...`);

        removeLiveTheme(liveTheme)
            .then(() => {
                utils.log(`Adding updated version ...`);
                return copyBaseTheme(baseTheme, liveTheme);
            })
            .then(() => wp.cmd(`theme activate ${themeName}`))
            .then(() => {
                utils.log(`Theme "${themeName}" successfully updated`, { type: 'success', top: 1 });
            })
            .catch(err => {
                utils.log(err, { type: 'error', top: 1 });
            });

    } else {
        utils.log(`Theme "${themeName}" does not exist`, { type: 'warning', top: 1 });
    }
};

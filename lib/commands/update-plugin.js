const path = require('path');
const fs = require('fs');
const fse = require('fs-extra');
const rimraf = require('rimraf');

const utils = require('../utils');
const wp = require('../wp');
const config = require('../config');

const wpBasePath = path.join(config.rootDir, 'wordpress-base');
const wpPath = path.join(config.rootDir, 'wordpress');

const removeLivePlugin = livePlugin => new Promise((resolve, reject) => {
    rimraf(livePlugin, err => {
        if (err) return reject(err);
        resolve();
    });
});

const copyBasePlugin = (src, dest) => new Promise((resolve, reject) => {
    fse.copy(src, dest, err => {
        if (err) return reject(err);
        resolve();
    });
});

module.exports = pluginName => {
    let basePlugin = path.join(wpBasePath, 'wp-content/plugins', pluginName),
        livePlugin = path.join(wpPath, 'wp-content/plugins', pluginName);

    if (fs.existsSync(basePlugin)) {
        console.log();
        utils.log(`Updating plugin "${pluginName}" ...`);
        utils.log(`Removing current version ...`);
        removeLivePlugin(livePlugin)
            .then(() => {
                utils.log(`Adding updated version ...`);
                return copyBasePlugin(basePlugin, livePlugin);
            })
            .then(() => {
                utils.log(`Reactivating plugin ...`);
                return wp.cmd(`plugin deactivate ${pluginName}`)
            })
            .then(() => wp.cmd(`plugin activate ${pluginName}`))
            .then(() => {
                console.log();
                utils.log(`Plugin "${pluginName}" successfully updated`, 'success');
            })
            .catch(err => {
                console.log();
                utils.log(err, 'error');
            });
    } else {
        utils.log(`Plugin "${pluginName}" does not exist`, 'warning');
    }
};

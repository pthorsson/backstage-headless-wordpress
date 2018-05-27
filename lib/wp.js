const fs = require('fs');
const path = require('path');
const { spawn, exec } = require('child_process');

const utils = require('./utils');
const data = require('./data');

const config = require('./config');

/**
 * WP-CLI wrapper function
 */
const wpcli = (cmd) => {
    return new Promise((resolve, reject) => {
        utils.log(`-> wp ${cmd}`, { type: 'blank' });

        exec(`php wp-cli.phar --path="./wordpress" ${cmd}`, (error, stdout, stderr) => {
            if (error) {
                utils.log(error, { type: 'error'});
                return reject();
            }

            resolve();
        });
    });
};

let wp = {

    cmd: cmd => wpcli(cmd),

    tasks: {

        /**
         * WP-CLI - Download WordPress core
         */
        downloadCore: () => (next, exit) => {
            utils.log('Downloading WordPress 4.9.4 ...');

            wp.cmd(`core download --version=4.9.4 --locale=en_US --force`)
                .then(next, () => {
                    utils.log('Could not download WordPress', { type: 'error'});
                    exit();
                });
        },

        /**
         * WP-CLI - Create WordPress config
         */
        createConfig: () => (next, exit) => {
            utils.log('Create WordPress config ...');

            wp.cmd(`core config --dbname="${data.database.database}" --dbuser="${data.database.user}" --dbpass="${data.database.password}" --dbhost="${data.database.host}"`)
                .then(next, () => {
                    utils.log('Could not create WordPress config', { type: 'error'});
                    exit();
                });
        },

        /**
         * WP-CLI - Ensure clean database
         */
        ensureDatabase: () => (next, exit) => {
            utils.log('Ensure clean database ...');

            wp.cmd(`db drop --yes`)
                .then(() => wp.cmd(`db create`))
                .then(next);
        },

        /**
         * WP-CLI - Installing WordPress
         */
        install: () => (next, exit) => {
            utils.log('Installing WordPress ...');

            wp.cmd(`core install --url="${data.wordpress.backendUrl}" --title="${data.theme.name}" --admin_user="${data.wordpress.username}" --admin_password="${data.wordpress.password}" --admin_email="${data.wordpress.email}" --skip-email`)
                .then(() => wp.cmd(`option update home "${data.wordpress.backendUrl}"`), () => {
                    utils.log('Could not install WordPress', { type: 'error'});
                    exit();
                })
                .then(() => wp.cmd(`option update siteurl "${data.wordpress.backendUrl}"`))
                .then(() => wp.cmd(`option update backstage_cors "{\\"enabled\\": true, \\"origins\\": [\\"${data.wordpress.frontendUrl}\\"]}" --format=json`))
                .then(() => wp.cmd(`theme activate "${data.theme.dir}"`))
                .then(next);
        },

        /**
         * WP-CLI - Cleaning up unnecessary files
         */
        cleanUp: () => (next, exit) => {
            utils.log('Cleaning up unnecessary files ...');

            wp.cmd(`plugin delete akismet hello`)
                .then(() => wp.cmd(`theme delete twentyfourteen twentyfifteen twentysixteen twentyseventeen`))
                .then(() => {
                    let plugins = fs.readdirSync(path.join('wordpress/wp-content/plugins'));
                    return wp.cmd(`plugin activate ${plugins.filter(plugin => plugin !== 'index.php').join(' ')}`);
                })
                .then(next);
        },

        /**
         * WP-CLI - Applying custom config
         */
        applyCustomConfig: () => (next, exit) => {
            utils.log('Applying custom config ...');

            wp.cmd(`acf sync`)
                .then(() => wp.cmd(`rewrite structure "%year%/%monthnum%/%day%/%postname%/"`))
                .then(() => () => {
                    let plugins = fs.readdirSync(path.join('wordpress/wp-content/plugins'));
                    return wp.cmd(`plugin activate ${plugins.filter(plugin => plugin !== 'index.php').join(' ')}`);
                })
                .then(next);
        },

        /**
         * WP-CLI - Adding some test data
         */
        addTestData: () => (next, exit) => {
            utils.log('Adding some test data ...');

            wp.cmd(`option update blogdescription "${data.theme.desc}"`)
                .then(() => wp.cmd(`post update "1" wordpress/wp-content/themes/${data.theme.dir}/post-content/sample-post.txt --post_title="Sample Post" --post_name=sample-post`))
                .then(() => wp.cmd(`post create wordpress/wp-content/themes/${data.theme.dir}/post-content/welcome.txt --post_type=page --post_status=publish --post_name=welcome --post_title="Congratulations!"`))
                .then(() => wp.cmd(`term update category "1" --name="Sample Category"`))
                .then(next);
        }
    }
};

module.exports = wp;

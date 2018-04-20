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
        utils.log(`-> wp ${cmd}`, 'blank');

        exec(`php wp-cli.phar --path="./wordpress" ${cmd}`, (error, stdout, stderr) => {
            if (error) {
                utils.log(error, 'error');
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

            wpcli(`core download --version=4.9.4 --locale=en_US --force`)
                .then(next, () => {
                    utils.log('Could not download WordPress', 'error');
                    exit();
                });
        },

        /**
         * WP-CLI - Create WordPress config
         */
        createConfig: () => (next, exit) => {
            utils.log('Create WordPress config ...');

            wpcli(`core config --dbname="${data.database.database}" --dbuser="${data.database.user}" --dbpass="${data.database.password}" --dbhost="${data.database.host}"`)
                .then(next, () => {
                    utils.log('Could not create WordPress config', 'error');
                    exit();
                });
        },

        /**
         * WP-CLI - Ensure clean database
         */
        ensureDatabase: () => (next, exit) => {
            utils.log('Ensure clean database ...');

            wpcli(`db drop --yes`)
                .then(() => wpcli(`db create`))
                .then(next);
        },

        /**
         * WP-CLI - Installing WordPress
         */
        install: () => (next, exit) => {
            utils.log('Installing WordPress ...');

            wpcli(`core install --url="http://localhost:9000" --title="${data.theme.name}" --admin_user="${data.wordpress.username}" --admin_password="${data.wordpress.password}" --admin_email="${data.wordpress.email}" --skip-email`)
                .then(() => wpcli('option update home "http://localhost:9000"'), () => {
                    utils.log('Could not install WordPress', 'error');
                    exit();
                })
                .then(() => wpcli('option update siteurl "http://localhost:9000"'))
                .then(() => wpcli(`theme activate "${data.theme.dir}"`))
                .then(next);
        },

        /**
         * WP-CLI - Cleaning up unnecessary files
         */
        cleanUp: () => (next, exit) => {
            utils.log('Cleaning up unnecessary files ...');

            wpcli(`plugin delete akismet hello filter-endpoints`)
                .then(() => wpcli(`theme delete twentyfourteen twentyfifteen twentysixteen twentyseventeen`))
                .then(() => {
                    let plugins = fs.readdirSync(path.join('wordpress/wp-content/plugins'));
                    return wpcli(`plugin activate ${plugins.filter(plugin => plugin !== 'index.php').join(' ')}`);
                })
                .then(next);
        },

        /**
         * WP-CLI - Applying custom config
         */
        applyCustomConfig: () => (next, exit) => {
            utils.log('Applying custom config ...');

            wpcli(`acf sync`)
                .then(() => wpcli(`rewrite structure "%year%/%monthnum%/%day%/%postname%/"`))
                .then(() => () => {
                    let plugins = fs.readdirSync(path.join('wordpress/wp-content/plugins'));
                    return wpcli(`plugin activate ${plugins.filter(plugin => plugin !== 'index.php').join(' ')}`);
                })
                .then(next);
        },

        /**
         * WP-CLI - Adding some test data
         */
        addTestData: () => (next, exit) => {
            utils.log('Adding some test data ...');

            wpcli(`option update blogdescription "${data.theme.desc}"`)
                .then(() => wpcli(`post update "1" wordpress/wp-content/themes/postlight-headless-wp/post-content/sample-post.txt --post_title="Sample Post" --post_name=sample-post`))
                .then(() => wpcli(`post create wordpress/wp-content/themes/postlight-headless-wp/post-content/welcome.txt --post_type=page --post_status=publish --post_name=welcome --post_title="Congratulations!"`))
                .then(() => wpcli(`term update category "1" --name="Sample Category"`))
                .then(() => wpcli(`menu create "Header Menu"`))
                .then(() => wpcli(`menu item add-post header-menu "1"`))
                .then(() => wpcli(`menu item add-post header-menu "2"`))
                .then(() => wpcli(`menu item add-term header-menu category "1"`))
                .then(() => wpcli(`menu location assign header-menu header-menu`))
                .then(next);
        }
    }
};

module.exports = wp;

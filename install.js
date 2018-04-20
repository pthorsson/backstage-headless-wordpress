let config = require('./lib/config').$set('rootDir', __dirname);;

const installer = require('./lib/installer');
const inquiries = require('./lib/inquiries');
const utils = require('./lib/utils');
const wp = require('./lib/wp');
const data = require('./lib/data');

inquiries().then(() => installer([

    // Download local wp-cli
    utils.downloadWpCli(),

    // Removing previous WordPress installation
    utils.deletePreviousInstallation(),

    // Clone WordPress base
    utils.cloneBase(),

    // Downloads WordPress 4.9.4
    wp.downloadCore(),

    // Create WordPress config
    wp.createConfig(),

    // Ensure clean database
    wp.ensureDatabase(),

    // Installing WordPress
    wp.install(),

    // Cleaning up unnecessary files
    wp.cleanUp(),

    // Applying custom config
    wp.applyCustomConfig(),

    // Adding some test data
    wp.addTestData(),

    () => {
        console.log();
        utils.log('All steps completed', 'success');

        console.log();
        utils.log('Quickstart:', 'blank');
        utils.log('  1. Run serve.sh', 'blank');
        utils.log('  2. Go to localhost:8080/wp-admin', 'blank');
        utils.log(`  3. Login with ${data.wordpress.username} / ${data.wordpress.password}`, 'blank');
    }

]));

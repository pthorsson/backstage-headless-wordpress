let config = require('../config');

const installer = require('../installer');
const inquiries = require('../inquiries');
const utils = require('../utils');
const wp = require('../wp');
const data = require('../data');

module.exports = () => inquiries().then(() => installer([

    // Download local wp-cli
    utils.tasks.downloadWpCli(),

    // Removing previous WordPress installation
    utils.tasks.deletePreviousInstallation(),

    // Clone WordPress base
    utils.tasks.cloneBase(),

    // Downloads WordPress 4.9.4
    wp.tasks.downloadCore(),

    // Create WordPress config
    wp.tasks.createConfig(),

    // Ensure clean database
    wp.tasks.ensureDatabase(),

    // Installing WordPress
    wp.tasks.install(),

    // Cleaning up unnecessary files
    wp.tasks.cleanUp(),

    // Applying custom config
    wp.tasks.applyCustomConfig(),

    // Adding some test data
    wp.tasks.addTestData(),

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

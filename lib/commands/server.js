const utils = require('../utils');
const wp = require('../wp');
const config = require('../config');

module.exports = () => {
    utils.log('Running web server on http://localhost:9000', { top: 1 });

    wp.cmd(`server --host=0.0.0.0 --port=9000`)
        .then(() => {
            utils.log('Done');
        }, (err) => {
            utils.log(err, 'error');
        });

    utils.log('Press Ctrl+c to stop the server', { top: 1 });
};

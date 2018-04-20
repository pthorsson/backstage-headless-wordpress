const path = require('path');
const fs = require('fs');

const config = require('../config');
const data = require('../data');
const utils = require('../utils');

const wpPath = path.join(config.rootDir, 'wordpress');

const originFile = path.join(wpPath, `wp-content/themes/${data.theme.dir}/inc/frontend-origin.php`);

module.exports = () => {
    if (!fs.existsSync(path.join(config.rootDir, 'allowed-origins.json'))) {
        utils.log(`Could not origins file "allowed-origins.json" - Create it and try again`, { type: 'error', top: 1 });
        utils.log(`Example:`);
        utils.log(`[`, { type: 'blank', top: 1 });
        utils.log(`    "http://localhost:9000"`, { type: 'blank' });
        utils.log(`]`, { type: 'blank' });
        return;
    }

    utils.log('Updating front end origin file ...', { top: 1 });

    let fileData = fs.readFileSync(originFile, 'utf8'),
        origins = require('../../allowed-origins.json');

    fileData = fileData.replace(/\/\*<\*\/.*\/\*>\*\//, `/*<*/'${origins.join("', '")}'/*>*/`);

    fs.writeFile(originFile, fileData, function(err) {
        if (err)  return utils.log(err, { type: 'error', top: 1 });
    
        utils.log('Front end origin file updated', { type: 'success', top: 1 });
    }); 

}

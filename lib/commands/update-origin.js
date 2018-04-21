const path = require('path');
const fs = require('fs');
const inquirer = require('inquirer');

const config = require('../config');
const data = require('../data');
const utils = require('../utils');

const wpPath = path.join(config.rootDir, 'wordpress');

const originFile = path.join(wpPath, `wp-content/themes/${data.theme.dir}/inc/frontend-origin.php`);

const ensureJson = () => new Promise((resolve, reject) => {
    if (!fs.existsSync(path.join(config.rootDir, 'allowed-origins.json'))) {
        utils.log(`"allowed-origins.json" does not exists`, { top: 1, bottom: 1 });
        
        inquirer.prompt([
            {
                type: 'confirm',
                name: 'create',
                message: '  Do you want to create "allowed-origins.json" ?',
            }
        ])
        .then(answers => {
            if (answers.create) {
                utils.log(`Type in the domains you want to allow as origins`, { top: 1 });
                utils.log(`Separate domains with a ,`, { type: 'blank', bottom: 1 });
                utils.log(`Example: http://localhost:9000, http://starlabs.local, http://starlabs.com`, { bottom: 1 });

                return inquirer.prompt([
                    {
                        type: 'input',
                        name: 'domains',
                        message: '  Add domains(s):'
                    }
                ]);
            } else {
                utils.log(`Allowed origins was not updated`, { top: 1, type: 'warning' });
                return Promise.reject();
            }
        })
        .then(answers => {
            let domains = answers.domains.split(/\s*[,]{1,}\s*/);

            fs.writeFile(path.join(config.rootDir, 'allowed-origins.json'), JSON.stringify(domains, null, 4), function(err) {
                if (err)  return utils.log(err, { type: 'error', top: 1 });
                resolve(domains);
            }); 
            
        }, () => reject('Aborted'));
    } else {
        utils.log('Using existing allowed-origins.json'), { top: 1 };
        resolve(require('../../allowed-origins.json'));
    }
});

const updateOrigins = domains => new Promise((resolve, reject) => {
    let fileData = fs.readFileSync(originFile, 'utf8');

    fileData = fileData.replace(/\/\*<\*\/.*\/\*>\*\//, `/*<*/'${domains.join("', '")}'/*>*/`);

    fs.writeFile(originFile, fileData, function(err) {
        if (err) return reject();
        resolve();
    }); 
});

module.exports = () => {
    ensureJson()
        .then(domains => updateOrigins(domains))
        .then(() => {
            utils.log('Front end origin file updated', { type: 'success', top: 1 });
        })
        .catch(err => {
            utils.log(err, { type: 'error', top: 1 });
        });
};

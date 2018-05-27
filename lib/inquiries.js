const inquirer = require('inquirer');
const defaultData = require('./default-data');
const data = require('./data');
const utils = require('./utils');

let inquiries = [
    {
        name: 'wordpress',
        info: 'Enter WordPress config\n',
        questions: [{
            type: 'input',
            name: 'username',
            message: '  Username:',
            default: () => defaultData.wordpress.username
        }, {
            type: 'input',
            name: 'password',
            message: '  Password:',
            default: () => defaultData.wordpress.password
        }, {
            type: 'input',
            name: 'email',
            message: '  Email:',
            default: () => defaultData.wordpress.email
        }, {
            type: 'input',
            name: 'backendUrl',
            message: '  Backend URL:',
            default: () => defaultData.wordpress.backendUrl
        }, {
            type: 'input',
            name: 'frontendUrl',
            message: '  Frontend URL:',
            default: () => defaultData.wordpress.frontendUrl
        }],
    }, {
        name: 'database',
        info: 'Enter database config\n',
        questions: [{
            type: 'input',
            name: 'host',
            message: '  Host:',
            default: () => defaultData.database.host
        }, {
            type: 'input',
            name: 'user',
            message: '  User:',
            default: () => defaultData.database.user
        }, {
            type: 'input',
            name: 'password',
            message: '  Password:',
            default: () => defaultData.database.password
        }, {
            type: 'input',
            name: 'database',
            message: '  Database:',
            default: () => defaultData.database.database
        }],
    },
];

module.exports = () => new Promise((resolve, reject) => {
    let _config = {};

    (function runTasks() {
        let inquiry = inquiries.shift();

        if (!inquiry) {
            return resolve(data);
        }

        utils.log(inquiry.info, { top: 1 });

        inquirer
            .prompt(inquiry.questions)
            .then((answers) => {
                data[inquiry.name] = answers;
                runTasks();
            });
    })();
});

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

    console.log('');

    (function runTasks() {
        let inquiry = inquiries.shift();

        if (!inquiry) {
            return resolve(data);
        }

        utils.log(inquiry.info);

        inquirer
            .prompt(inquiry.questions)
            .then((answers) => {
                data[inquiry.name] = answers;
                console.log('');
                runTasks();
            });
    })();
});

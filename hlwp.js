#!/usr/bin/env node

require('./lib/config').$set('rootDir', __dirname);

const program = require('commander');

const install = require('./lib/commands/install');
const updateOrigin = require('./lib/commands/update-origin');
const updatePlugin = require('./lib/commands/update-plugin');

const runProgram = p => {
    if (p.install)      return install();
    if (p.updateOrigin) return updateOrigin();
    if (p.updatePlugin) return updatePlugin(p.updatePlugin);

    p.help();
};

program
    .version('1.0.0', '-v, --version')
    .option('-i, --install', 'preforms a headless WordPress intallation')
    .option('-o, --update-origin', 'updates front end origin')
    .option('-p, --update-plugin [plugin]', 'updates an plugin')
    .parse(process.argv);

runProgram(program);

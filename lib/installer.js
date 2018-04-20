const utils = require('./utils');

module.exports = steps => (function execStep() {
    let currentStep = steps.shift();

    if (!currentStep) return;

    currentStep(execStep, exitMessage => utils.log(exitMessage));
})();
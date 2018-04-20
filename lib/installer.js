module.exports = (steps) => (function _execSteps() {
    let currentStep = steps.shift();

    if (!currentStep) {
        console.log('All steps completed');
        return;
    }

    currentStep(() => {
        _execSteps();
    }, exitMessage => {
        console.log('Installation exited', exitMessage);
    });
})();
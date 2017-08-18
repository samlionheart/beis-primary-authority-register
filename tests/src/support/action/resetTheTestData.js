/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = (done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    browser.url('/user/login');
    browser.setValue('#edit-name', 'dadmin');
    browser.setValue('#edit-pass', 'password');
    browser.click('#edit-submit');
    browser.url('/admin/par-data-test-reset');
    browser.url('/user/logout');
    done();
};
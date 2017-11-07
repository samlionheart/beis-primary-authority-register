const config = require('./wdio.conf.js').config;

config.capabilities = [{
  browserName: 'chrome',
  chromeOptions: {
    binary: '/usr/bin/google-chrome',
    args: ['--headless', '--no-sandbox', '--disable-gpu', '--window-size=1200,2000']
  }
}];
config.screenshotPath = './errorShots/';
config.services = ['selenium-standalone'];
config.baseUrl = 'http://127.0.0.1:80';
config.tags = '@ci, ~@Pending, ~@setup, ~@deprecated, ~@Bug';
config.cucumberOpts.failFast = true;
config.bail = 1;

function sleep(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}

config.before = function before() {
	
	console.log('Pausing before starting tests...');
	await sleep(12000);
	
    /**
     * Setup the Chai assertion framework
     */
    const chai = require('chai');

    global.expect = chai.expect;
    global.assert = chai.assert;
    global.should = chai.should();
}

exports.config = config;


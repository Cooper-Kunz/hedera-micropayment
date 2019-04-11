const data = require('../e2e/data/login')

module.exports = {

    'Logged in user clicks list of post reloads page 2 times': function(browser) {
        browser
        .url('http://localhost:8080/wp-login.php?loggedout=true')
        .waitForElementVisible('body')
        .setValue('input[type=text]', data.username)
        .setValue('input[type=password]', data.password)
        .click('input[name=wp-submit]')
        .pause(1000)
        .waitForElementVisible('body')
        .click('li#wp-admin-bar-site-name')
        .pause(1000)
        .waitForElementVisible('body')
        // meta title
        .getTitle(function (title) {
            this.assert.ok(title.includes("whatever"));
        })
        // click to any article
        .click('.content-area .site-main #post-1 .entry-header .entry-title a:link')
        .pause(1000)
        .waitForElementVisible('body')
        // refresh page 3x free articles for micropayment tag to appear
        .refresh()
        .pause(1000)
        .refresh()
        .pause(1000)
        .refresh()
        .pause(1000)
        .waitForElementVisible('body', 10000)
        .execute(function() {
            browser.pause(10000)
            let blah = document.getElementsByTagName('hedera-micropayment')
            console.log("111", blah)
            console.log("222", blah[0])
            console.log("333", blah[0].dataset.paymentserver)
            this.assert.ok(blah[0].dataset.extensionid, 'ligpaondaabclfigagcifobaelemiena')
        })
        .pause(1000)
        .waitForElementVisible('body')
        .end();
    }
};

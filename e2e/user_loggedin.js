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
        .waitForElementVisible('body')
        .execute(function() {
            let els = document.getElementsByTagName('hedera-micropayment')
            // this.assert.ok(els[0].getAttribute('data-contentid'), 1)
            for (var i=0; i<els.length; i++) {
                if (i === 0) {
                    console.log("els", els[i])
                    this.assert.ok(els[i].getAttribute('data-contentid'), 1)
                }
            }
        })
        .pause(1000)
        .waitForElementVisible('body')
        .end();
    }
};

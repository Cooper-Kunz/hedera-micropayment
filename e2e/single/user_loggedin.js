module.exports = {

    'Wordpress login Functionality' : function (browser) {

        //create an object for login
        var login = browser.page.login();
        //execute the login method from //tests/pages/login.js file
        login.navigate().login();
    
        //You can continue with your tests below:
        // Also, you can use similar Page objects to increase reusability
        browser
        .pause(3000)
        .end();
        },
        
    'Logged in user clicks list of post reloads page 2 times': function(browser) {
        browser
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
        // to do check if micropayment tag appears
        // .useCss()
        .element('css selector', 'hedera-micropayment', function(result) {
            console.log("result value", result)
            // console.log("result value", result.getAttribute('data-contentid'))
            // this.assert.equal(result['data-contentid'], 1);
        })
        // .verify.attributeEquals('#a_checkbox', 'checked', 'true');){
        //     browser.expect.element('body').to.not.be.present;
        .execute(function() {
            let els = document.getElementsByTagName('hedera-micropayment')
            for (var i=0; i<els.length; i++) {
                console.log("els i", i)
                if (i === 1) {
                    console.log("els", els[i])
                    // this.assert.ok(els[i].getAttribute('data-contentid'), 1)
                }
            }
        })
        .getLog('browser', function(logEntriesArray) {
            if (logEntriesArray.length) {
                console.log('Log length: ' + logEntriesArray.length);
                logEntriesArray.forEach(function(log) {
                    console.log(
                        '[' + log.level + '] ' + log.timestamp + ' : ' + log.message
                    )
                })
            }
        })
        .pause(1000)
        .waitForElementVisible('body')
        .end();
    }
};

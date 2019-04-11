const data = require('../data/login')

module.exports = {

    // 'Publisher enable Hedera micropayment plugin': function(browser) {
    //     browser
    //     .url('http://localhost:8080/wp-admin')
    //     .waitForElementVisible('body')
    //     .setValue('input[type=text]', data.username)
    //     .setValue('input[type=password]', data.password)
    //     .click('input[name=wp-submit]')
    //     .pause(1000)
    //     .waitForElementVisible('body')
    //     .click('ul#adminmenu li#menu-posts')
    //     .pause(1000)
    //     .getTitle(function(title) {
    //         this.assert.ok(title.includes("Posts"));
    //     })
    //     //todo enable the checkbox
    //     .end();
    // },
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

    'Publisher enable Hedera micropayment plugin, Checkbox is checked' : function (client) {
        client
        .click('ul#adminmenu li#menu-posts')
        .pause(1000)
        .getTitle(function(title) {
            this.assert.ok(title.includes("Posts"));
        })
        //todo enable the checkbox
        .waitForElementVisible("body", 1000)
        // .verify.attributeEquals('#b_checkbox', 'checked', null)
        // .verify.attributeEquals('#b_checkbox', 'checked', 'null')
        // .verify.attributeEquals('#b_checkbox', 'checked', 'false')
        // .verify.attributeEquals('#b_checkbox', 'checked', false)
        // .verify.attributeEquals('#b_checkbox', 'checked', '')
        .verify.elementPresent('#b_checkbox')
        .verify.elementNotPresent('#b_checkbox:checked')
        // .getLog('browser', function(logEntriesArray) {
            //     if (logEntriesArray.length) {
            //         console.log('Log length: ' + logEntriesArray.length);
            //         logEntriesArray.forEach(function(log) {
            //             console.log(
            //                 '[' + log.level + '] ' + log.timestamp + ' : ' + log.message
            //             )
            //         })
            //     }
            // })
        .end();
      }
};

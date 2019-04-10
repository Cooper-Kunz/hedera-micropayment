const data = require('../e2e/data/login')

module.exports = {

    'Publisher setting options for Hedera micropayment plugin': function(browser) {
        browser
        .url('http://localhost:8080/wp-admin')
        .waitForElementVisible('body')
        .setValue('input[type=text]', data.username)
        .setValue('input[type=password]', data.password)
        .click('input[name=wp-submit]')
        .pause(1000)
        .waitForElementVisible('body')
        .click('li#menu-settings')
        .pause(1000)
        .execute(function() {
            var els = document.getElementsByTagName("a");
            for (var i = 0, l = els.length; i < l; i++) {
                var el = els[i];
                var substring = "options-general.php?page=hedera-micropayment"
                if (el.href.indexOf(substring) !== -1) {
                    el.click()
                }
            }
        })
        .waitForElementVisible('body')
        .pause(1000)
        .getTitle(function (title) {
            this.assert.ok(title.includes("Hedera Micropayment Settings"));
        })
        // reset values
        .execute(function () {
            // loop through all setttings and reset input data
            var inputs = document.getElementsByTagName('input')
            for (var i = 0; i<inputs.length; i++) {
                inputs[i].value = '';
            }    
        })
        // input fields and save
        .setValue('input[name=hedera_micropayment_free]', '3')
        .pause(1000)
        .setValue('input[name=hedera_micropayment_extension_id]', 'ligpaondaabclfigagcifobaelemiena')
        .pause(1000)
        .setValue('input[name=hedera_micropayment_type]', 'article')
        .pause(1000)
        .setValue('input[name=hedera_micropayment_payment_server', 'http://localhost:8099')
        // .assert.urlContains( '8099' )
        .pause(1000)
        .execute(function() {
            // set the values of recipient list
            let els = document.getElementsByClassName('input_fields_wrap')
            for (var i=0; i<els.length; i++) {
                var accSubStr = '[account]'
                var amtSubStr = '[amount]'
                if (els[i].nodeName.indexOf(accSubStr) !== -1) {
                    // if class name of i is account
                    // .setValue('hedera_micropayment_recipient[0][amount]', 333)
                    // .setValue('hedera_micropayment_recipient[0][account]', 0.0.9)
                }
                // else class name of i is amount
            }
        })
        .click('#submit')
        .end();
    }
};
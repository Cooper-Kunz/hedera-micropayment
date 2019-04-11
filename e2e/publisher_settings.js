const data = require('../e2e/data/login')

module.exports = {

    'Publisher setting options for Hedera micropayment plugin': function(browser) {
        browser
        .url('http://localhost:8080/wp-admin')
        .waitForElementVisible('body')
        .setValue('input[type=text]', data.username)
        .setValue('input[type=password]', data.password)
        .click('input[name=wp-submit]')
        .pause(500)
        .waitForElementVisible('body')
        .click('li#menu-settings')
        .pause(500)
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
        .pause(500)
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
        .pause(500)
        .setValue('input[name=hedera_micropayment_extension_id]', 'ligpaondaabclfigagcifobaelemiena')
        .pause(500)
        .setValue('input[name=hedera_micropayment_type]', 'article')
        .pause(500)
        .setValue('input[name=hedera_micropayment_payment_server]', 'http://localhost:8099')
        // .assert.urlContains( '8099' )
        .pause(500)
        .execute(function() {
            var blah = document.querySelectorAll('input[name^=hedera_micropayment_recipient]')
            for (var i = 0; i < blah.length; i++) {
              if (i%2) {
                //amount
                blah[i].value = `${(i+1)*100 + (i+1)*10 + (i+1)}`
            } else {
              //account
              blah[i].value = '0.0.'+(i+1)+''
              }
            }
        })
        .pause(2000)
        .click('#submit')
        .end();
    }
};
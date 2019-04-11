const data = require('../data/login')

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
        
    'Publisher setting options for Hedera micropayment plugin': function(browser) {
        browser
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
        .element('css selector', 'input_fields_wrap', function(result) {
            console.log("result value", result.length)
            console.log("result value", result.getAttribute("hedera_micropayment_recipient[0][account]"))
            // this.assert.equal(result['data-contentid'], 1);
        })
        .execute(function() {
            // set the values of recipient list
            let els = document.getElementsByClassName('input_fields_wrap')
            for (var i=0; i<els.length; i++) {
              
                // if class name of i is account
                var accountVal = els[i].getAttribute('hedera_micropayment_recipient[' + i + '][account]')
                accountVal.attr.data('').setValue('hedera_micropayment_recipient[0][account]', '0.0.'+(i+1))
                
                // else class name of i is amount
                var amountVal = els[i].getAttribute('hedera_micropayment_recipient[' + i + '][amount]')
                amountVal.setValue('hedera_micropayment_recipient[0][amount]', ((i+1)*100) + ((i+1)*10) + (i+1))
            }
        })
        .click('#submit')
        .end();
    }
};
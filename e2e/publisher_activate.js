const data = require('../e2e/data/login')

module.exports = {

    'Publisher activate Hedera micropayment plugin': function(browser) {
        browser
        .url('http://localhost:8080/wp-admin')
        .waitForElementVisible('body')
        .setValue('input[type=text]', data.username)
        .setValue('input[type=password]', data.password)
        .click('input[name=wp-submit]')
        .pause(1000)
        .waitForElementVisible('body')
        .click('ul#adminmenu li#menu-plugins')
        .pause(1000)
        .getTitle(function(title) {
            this.assert.ok(title.includes("Plugins"));
        })
        //check if activate, deactivate
        .execute(function() {
            var els = document.getElementsByTagName('a')
            for (var i = 0; i< els.length; i++) {
                var el = els[i]
                var substring = 'plugins.php?action=deactivate&plugin=hedera-micropayment'
                if(el.href.indexOf(substring) !== -1) {
                    el.click()
                }
            }
        })
        .waitForElementVisible('body')
        .pause(1000)
        .execute(function() {
            var els = document.getElementsByTagName('a')
            for (var i = 0; i< els.length; i++) {
                var el = els[i]
                var substring = 'plugins.php?action=activate&plugin=hedera-micropayment'
                if(el.href.indexOf(substring) !== -1) {
                    el.click()
                }
            }
        })
        .waitForElementVisible('body')
        .pause(1000)
        .end();
    }

    
};

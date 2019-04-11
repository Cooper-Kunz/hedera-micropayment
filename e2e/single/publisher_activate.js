module.exports = {

    'Wordpress login Functionality' : function (browser) {

        //create an object for login
        var login = browser.page.login();
        //execute the login method from //tests/pages/login.js file
        login.navigate().login();
    
        //You can continue with your tests below:
        // Also, you can use similar Page objects to increase reusability
        browser
        .useXpath() // every selector now must be xpath
        .pause(3000)
        .end();
        },

    'Publisher activate Hedera micropayment plugin': function(browser) {
        browser
        .useCss() // we're back to CSS now
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

const data = require('../data/login')

var loginCommands = {
    login: function() {
        return this.waitForElementVisible('body', 1000)
        .verify.visible('@username')
        .verify.visible('@password')
        .verify.visible('@submit')
        .setValue('@username', data.username)
        .setValue('@password', data.password)
        .waitForElementVisible('body', 1000)
    }
}

module.exports = {
    comands: [loginCommands],
    url:function() {
        return 'http://localhost:8080/wp-admin'
    },
    elements: {
        username: {
            selector: '//input[@name=\'log\']',
            locateStrategy: 'xpath'
        },
        password: {
            selector: '#password',
            locateStrategy: 'xpath'
          },
          submit: {
            selector: 'input[type=submit]',
            locateStrategy: 'xpath'
          }
    }
}
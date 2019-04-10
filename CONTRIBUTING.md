# Running Tests

Pre-requisites: 

* mariadb or mysql server
* composer

Installing php dependencies with composer and setting up for tests.

```
# cd to hedera-micropayment plugin directory where composer.json exist

# php dependencies for our plugin
composer install

# one-time test database setup
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# running tests
./vendor/bin/phpunit
```


# Clone and create a new demo-wordpress for database test

    # one-time test database setup
    ```
    bash bin/simple-test.sh e2e-test-db root '' localhost latest
    ```
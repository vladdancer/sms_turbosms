INSTALLATION
============
1. Install module as usual.
2. Setup module.
   - Register in the system http://turbosms.ua/. Add the sender's signature.
      In the section "Connecting to the gateway" set up a login and password
      gateway, select connection method - SQL. 10 test sms with registration
      are provided free.
  - Specify in the **settings.php** settings for connecting to a server table

**Example:**
```php

$databases = array(
  'default' => array(
    'default' =>  array(
      'database' => 'your_database',
        'username' => 'your_username',
        'password' => 'your_password',
        'host' => 'localhost',
      'port' => '',
        'driver' => 'mysql',
        'prefix' => '',
      ),
    ),
  'sms_turbosms' => array(
    'default' => array(
      'database' => 'users',
      'username' => 'your_login_gateway',    // login gateway
      'password' => 'your_password_gateway', // password gateway
      'collation' => 'utf8_general_ci',
      'host' => '77.120.116.10',
      'port' => '',
      'driver' => 'mysql',
      'prefix' => '',
      'pdo' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    ),
  ),
);
```

3. Enable the TurboSMS module in the administration tools.

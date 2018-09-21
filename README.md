# cli-logger

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist apollo11/cli-logger "*"
```

or add

```
"apollo11/cli-logger": "*"
```

to the require section of your `composer.json` file.

## Basic usage ##

```php
include_once 'CliColor.php';
include_once 'CliLogger.php';

$logger = new \apollo11\cliLogger\CliLogger([
    'logFilePath' => dirname(__FILE__) . '/logs',
    'logFileName' => 'test12.log',
    'logFileDateFormat' => "Y_m_d_H_i_s",
    'logFileTemplate' => '{date}_{fileName}',
    'logTextDateFormat' => 'Y-m-d',
    'logTextTemplate' => '{date} [{type}] - {message}' . PHP_EOL,
]);

for ($i = 0; $i < 20; $i++){
    $logger->log("My test $i", \apollo11\cliLogger\CliColor::F_WHITE, \apollo11\cliLogger\CliColor::B_GREEN);
}

```
## Logger Options ##

<h5>Log file creation types (const)</h5>
```php
FILE_CREATE_TYPE_BY_TIME     //recreate log file by time
FILE_CREATE_TYPE_BY_SIZE    //recreate log file by size
```
<h5>Color</h5> 
```php
enableColors       //colored text for logs , deafult value true
```
<h5>Log file recreation type</h5>
```php
fileCreateType     //log file creation type  , default value   "FILE_CREATE_TYPE_BY_TIME"
```

<h5>Log file recreation units</h5>
```php
Add one of this properties if file recreation type set to   "FILE_CREATE_TYPE_BY_TIME"

fileReCreateMinutes     //  (integer)
fileReCreateHours       //  (integer)
fileReCreateDays        //  (integer)
fileReCreateMonths      //  (integer)
fileReCreateYears       //  (integer)
```
<h5>Log file recreation size</h5>
```php
Add this property if file recreation type set to   "FILE_CREATE_TYPE_BY_SIZE"
    
filReCreateSize = 900; //size in bytes
```   
<h5>Log file attributes</h5>
```php
logFilePath             // full path to log file
logFileName             // log file name   "example.log"
logFileDateFormat       // log file date format default value   "Y_m_d"
logFileTemplate         // log file template deafault value   "{date}_{fileName}"
``` 
<h5>Log text attributes</h5>   
```php
logTextDateFormat       // log text date format default value   "Y-m-d H:i:s"
logTextTemplate         // log text template default value   "[ {date} | {type} ] - {message} " . PHP_EOL
```


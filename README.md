# File-logger

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist apollo11/file-logger "*"
```

or add

```
"apollo11/file-logger": "*"
```

to the require section of your `composer.json` file.

## Basic usage ##

```php
include_once 'FileColor.php';
include_once 'FileLogger.php';

$logger = new \apollo11\fileLogger\FileLogger([
    'logFilePath' => dirname(__FILE__) . '/logs',
    'logFileName' => 'test12.log',
    'logFileDateFormat' => "Y_m_d_H_i_s",
    'logFileTemplate' => '{date}_{fileName}',
    'logTextDateFormat' => 'Y-m-d',
    'logTextTemplate' => '{date} [{type}] - {message}' . PHP_EOL,
]);

for ($i = 0; $i < 20; $i++){
    $logger->log("My test $i", \apollo11\fileLogger\FileColor::F_WHITE, \apollo11\fileLogger\FileColor::B_GREEN);
}

```
## Logger Options ##

<h5>Log file creation types (const)</h5>

```
FILE_CREATE_TYPE_BY_TIME     //recreate log file by time
FILE_CREATE_TYPE_BY_SIZE    //recreate log file by size
```
<h5>Color</h5>

```
enableColors       //colored text for logs , deafult value true
```

<h5>Save latest logs</h5>

```
saveLatestFileNumber       // bool/integer , deafult value 100 (will save only last 100 log files)
```

<h5>Force create</h5>

```
//Force create directory if directory does not exist
//Throws error if directory path was invalid  
      
forceCreateDirectory   // bool default value false 
```

<h5>Log file recreation type</h5>

```
fileCreateType     //log file creation type  , default value   "FILE_CREATE_TYPE_BY_TIME"
```

<h5>Log file recreation days</h5>

```
Add  this properties if file recreation type set to   "FILE_CREATE_TYPE_BY_TIME"

fileReCreateDays        //  (integer)

```
<h5>Log file recreation size</h5>

```
Add this property if file recreation type set to   "FILE_CREATE_TYPE_BY_SIZE"

filReCreateSize = 900; //size in bytes
```   
<h5>Log file attributes</h5>

```
logFilePath             // full path to log file
logFileName             // log file name   "example.log"
logFileDateFormat       // log file date format default value   "Y_m_d"
logFileTemplate         // log file template deafault value   "{date}_{fileName}"
```
<h5>Log text attributes</h5>  

```
logTextDateFormat       // log text date format default value   "Y-m-d H:i:s"
logTextTemplate         // log text template default value   "[ {date} | {type} ] - {message} " . PHP_EOL
```

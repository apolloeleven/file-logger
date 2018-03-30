# cli-logger

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

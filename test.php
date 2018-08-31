<?php $refresh  = 1; ?>
<meta http-equiv="refresh" content="<?php echo $refresh * 60 ?>" >
<?php
/**
 * Created by PhpStorm.
 * User: koco
 * Date: 3/29/18
 * Time: 1:10 PM
 */
include_once 'CliColor.php';
include_once 'CliLogger.php';

$logger = new \apollo11\cliLogger\CliLogger([
    'enableColors' => false,
    'logFilePath' => dirname(__FILE__) . '/logs',
    'logFileName' => 'mylogfile.log',
    'logFileDateFormat' => "Y_m_d_H_i_s",
    'logFileTemplate' => '{date}_{fileName}',
    'logTextDateFormat' => 'Y-m-d',
    'logTextTemplate' => '{date} [{type}] - {message}' . PHP_EOL,
    'fileCreateType' => 3,
    'fileReCreateMinutes' => 1,
    'saveLatestFileNumber' => 4
]);

for ($i = 0; $i < 20; $i++){
    $logger->log("My test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasdMy test asdasdasdasd $i", \apollo11\cliLogger\CliColor::F_WHITE, \apollo11\cliLogger\CliColor::B_GREEN);
//    $logger->error("My test asdasdasdasd $i");
//    $logger->success("My test asdasdasdasd $i");
}
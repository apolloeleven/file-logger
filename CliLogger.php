<?php
/**
 * Created by PhpStorm.
 * User: koco
 * Date: 3/29/18
 * Time: 12:39 PM
 */

namespace apollo11\cliLogger;


class CliLogger
{

    const FILE_CREATE_TYPE_BY_TIME = 1;

    const FILE_CREATE_TYPE_BY_SIZE = 2;

    public $enableColors = true;

    public $fileCreateType = self::FILE_CREATE_TYPE_BY_SIZE;

    public $fileReCreateMinutes = 1;
    public $fileReCreateHours = 0;
    public $fileReCreateDays = 0;
    public $fileReCreateMonths = 0;
    public $fileReCreateYears = 0;

    public $filReCreateSize = 900;

    // Log file attributes
    public $logFilePath;
    public $logFileName = 'example.log';
    public $logFileDateFormat = "Y_m_d";
    public $logFileTemplate = "{date}_{fileName}";

    // Log text attributes
    public $logTextDateFormat = "Y-m-d H:i:s";
    public $logTextTemplate = "[ {date} | {type} ] - {message} " . PHP_EOL;


    public function __construct($config)
    {
        if (!empty($config)) {
            $this->configure($this, $config);
        }

        $this->logFilePath = rtrim($this->logFilePath, '/');
    }


    public function log($message, $fColor = CliColor::F_WHITE, $bColor = null, $type = 'LOG')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), $fColor, $bColor);
    }


    public function error($message, $type = 'ERROR')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), CliColor::F_RED);
    }


    public function info($message, $type = 'INFO')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), CliColor::F_LIGHT_BLUE);
    }


    public function success($message, $type = 'SUCCESS')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), CliColor::F_LIGHT_GREEN);
    }


    private function writeLog($message, $fColor, $bColor = null)
    {
        if (!file_exists($this->logFilePath)) {
            throw new \Exception('logFilePath is invalid');
        }
        if($this->enableColors === true){
            $message = CliColor::getColoredString($message, $fColor, $bColor);
        }

        $expiredLogFile = $this->checkFileCreation();

        file_put_contents($this->logFilePath . '/' . $this->processFileTemplate($expiredLogFile), $message, FILE_APPEND);

        return $message;
    }


    private function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }


    private function processFileTemplate($expiredLogFile)
    {

        $parts = [
            '{date}' => date($this->logFileDateFormat),
            '{fileName}' => $this->logFileName
        ];

        $fileName = strtr($this->logFileTemplate, $parts);

        $latestFile = $this->getLatestLogFile();
        if($latestFile && $expiredLogFile === false){
            $fileName = $latestFile;
        }

        if($expiredLogFile){
            $pathInfo = pathinfo($fileName);
            if($this->fileCreateType === self::FILE_CREATE_TYPE_BY_SIZE){
                if($fileName === $expiredLogFile){
                    $fileName = $expiredLogFile = $pathInfo['filename'] . '_' .time(). '.'. $pathInfo['extension'];
                }else{
                    $expiredLogFile = false;
                }
            }elseif ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_TIME){
                $expiredLogFile = false;
            }
        }

        return $expiredLogFile ?: $fileName;
    }


    private function processLogTextTemplate($message, $type = 'LOG')
    {
        $parts = [
            '{date}' => date($this->logTextDateFormat),
            '{type}' => $type,
            '{message}' => $message,
        ];

        return strtr($this->logTextTemplate, $parts);
    }


    private function checkFileCreation()
    {
        $logFileName = $this->getLatestLogFile();
        $logFilePath = $this->logFilePath . '/' . $logFileName;
        if ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_SIZE) {
            if (file_exists($logFilePath) && filesize($logFilePath) >= $this->filReCreateSize) {
                return $logFileName;
            }
        } elseif ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_TIME) {
            // TODO get first create date from file
            $lastModified = filemtime($logFilePath);
            if (file_exists($logFilePath)
                && (time() - $lastModified)
                >= (
                      ($this->fileReCreateMinutes * 60)
                    + ($this->fileReCreateHours * 3600)
                    + ($this->fileReCreateDays * 86400)
                    + ($this->fileReCreateMonths * 2592000)
                    + ($this->fileReCreateYears * 31536000)
                )
            ) {
                return $logFileName;
            }
        }

        return false;
    }


    private function getLatestLogFile()
    {
        $files = [];
        if ($handle = opendir($this->logFilePath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != ".gitignore") {
                    $files[filemtime($this->logFilePath . '/' . $file)] = $file;
                }
            }
            closedir($handle);
            // sort
            sort($files);

            return end($files);
        }

        return false;
    }
}
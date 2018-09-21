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


    public $saveLatestFileNumber = 1;

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


    /**
     * CliLogger constructor.
     * @param $config
     */
    public function __construct($config)
    {
        if (!empty($config)) {
            $this->configure($this, $config);
        }

        $this->logFilePath = rtrim($this->logFilePath, '/');
    }


    /**
     * @param $message
     * @param string $fColor
     * @param null $bColor
     * @param string $type
     * @return string
     */
    public function log($message, $fColor = CliColor::F_WHITE, $bColor = null, $type = 'LOG')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), $fColor, $bColor);
    }


    /**
     * @param $message
     * @param string $type
     * @return string
     */
    public function error($message, $type = 'ERROR')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), CliColor::F_RED);
    }


    /**
     * @param $message
     * @param string $type
     * @return string
     */
    public function info($message, $type = 'INFO')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), CliColor::F_LIGHT_BLUE);
    }


    /**
     * @param $message
     * @param string $type
     * @return string
     */
    public function success($message, $type = 'SUCCESS')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), CliColor::F_LIGHT_GREEN);
    }


    /**
     * @param $message
     * @param $fColor
     * @param null $bColor
     * @return string
     * @throws \Exception
     */
    private function writeLog($message, $fColor, $bColor = null)
    {
        if (!file_exists($this->logFilePath)) {
            throw new \Exception('logFilePath is invalid');
        }
        if ($this->enableColors === true) {
            $message = CliColor::getColoredString($message, $fColor, $bColor);
        }

        $expiredLogFile = $this->checkFileCreation();

        file_put_contents($this->logFilePath . '/' . $this->processFileTemplate($expiredLogFile), $message, FILE_APPEND);


        if ($this->saveLatestFileNumber > 1) {
            /*check old logs and delete them*/
            $this->deleteOldLogs();

        }


        return $message;
    }


    /**
     * @param $object
     * @param $properties
     * @return mixed
     */
    private function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }


    /**
     * @param $expiredLogFile
     * @return bool|mixed|string
     */
    private function processFileTemplate($expiredLogFile)
    {

        $parts = [
            '{date}' => date($this->logFileDateFormat),
            '{fileName}' => $this->logFileName
        ];

        $fileName = strtr($this->logFileTemplate, $parts);

        $latestFile = $this->getLatestLogFile();
        if ($latestFile && $expiredLogFile === false) {
            $fileName = $latestFile;
        }

        if ($expiredLogFile) {

            $pathInfo = pathinfo($fileName);
            if ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_SIZE) {
                if ($fileName === $expiredLogFile) {
                    $fileName = $expiredLogFile = $pathInfo['filename'] . '_' . time() . '.' . $pathInfo['extension'];
                } else {
                    $expiredLogFile = false;
                }
            } else {
                $expiredLogFile = false;
            }
        }


        return $expiredLogFile ?: $fileName;
    }


    /**
     * @param $message
     * @param string $type
     * @return string
     */
    private function processLogTextTemplate($message, $type = 'LOG')
    {
        $parts = [
            '{date}' => date($this->logTextDateFormat),
            '{type}' => $type,
            '{message}' => $message,
        ];

        return strtr($this->logTextTemplate, $parts);
    }


    function deleteOldLogs()
    {
        if (is_dir($this->logFilePath)) {

            $logFilePath = $this->logFilePath;
            $logFileName = $this->logFileName;
            $files = glob("${$logFilePath}/*.${$logFileName}");
            $allFilesArray = [];

            foreach ($files as $key => $file) {
                $allFilesArray[$key]['time'] = filemtime($this->logFilePath . '/' . $file);
                $allFilesArray[$key]['name'] = $file;
            }

            usort($allFilesArray, function ($a, $b) {
                return $b['time'] - $a['time'];
            });

            if (count($allFilesArray) > $this->saveLatestFileNumber && $this->saveLatestFileNumber > 1) {
                for ($i = 0; $i < $this->saveLatestFileNumber; $i++) {
                    if ($allFilesArray[$i]['time'] >= date($this->logFileDateFormat)) {
                        unset($allFilesArray[$i]);
                    }
                }

                foreach ($allFilesArray as $deteFileNames) {
                    $file = $this->logFilePath . '/' . $deteFileNames['name'];
                    if (file_exists($file)) {
                        unlink($file);
                        echo 'Deleted file: Time:' . $deteFileNames['time'] . " Name: " . $deteFileNames['name'] . "<br>";
                    }
                }
            }


        }
    }


    /**
     * @return bool|mixed
     */
    private function checkFileCreation()
    {

        $logFileName = $this->getLatestLogFile();
        $logFilePath = $this->logFilePath . '/' . $logFileName;
        $lastModified = filemtime($logFilePath);
        $ex = time() - $lastModified;
        $t = (
            ($this->fileReCreateMinutes * 60)
            + ($this->fileReCreateHours * 3600)
            + ($this->fileReCreateDays * 86400)
            + ($this->fileReCreateMonths * 2592000)
            + ($this->fileReCreateYears * 31536000)
        );

        if ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_SIZE) {
            if (file_exists($logFilePath) && filesize($logFilePath) >= $this->filReCreateSize) {
                return $logFileName;
            }
        } else {
            // TODO get first create date from file

            if (file_exists($logFilePath) && ($ex >= $t)) {
                return $logFileName;
            }
        }

        return false;
    }


    /**
     * @return bool|mixed
     */
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
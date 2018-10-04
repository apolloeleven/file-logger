<?php
/**
 * Created by PhpStorm.
 * User: koco
 * Date: 3/29/18
 * Time: 12:39 PM
 */

namespace apollo11\fileLogger;


class FileLogger
{
    //log file creation types
    const FILE_CREATE_TYPE_BY_TIME = 1;

    const FILE_CREATE_TYPE_BY_SIZE = 2;

    //latest log count to save
    // bool/integer
    public $saveLatestFileNumber = 100;

    //color option for log text
    public $enableColors = true;

    //log file creation type property
    public $fileCreateType = self::FILE_CREATE_TYPE_BY_SIZE;

    //Log file recreation units
    public $fileReCreateMinutes = 1;
    public $fileReCreateHours = 0;
    public $fileReCreateDays = 0;
    public $fileReCreateMonths = 0;
    public $fileReCreateYears = 0;

    //Log file recreation size
    public $filReCreateSize = 900; //size in bytes

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
     * Log message
     *
     * Log message with color parameters in log file
     *
     * @param $message
     * @param string $fColor
     * @param null $bColor
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function log($message, $fColor = FileColor::F_WHITE, $bColor = null, $type = 'LOG')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), $fColor, $bColor);
    }


    /**
     * Log error
     *
     * @param $message
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function error($message, $type = 'ERROR')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), FileColor::F_RED);
    }


    /**
     * Log info
     *
     * @param $message
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function info($message, $type = 'INFO')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), FileColor::F_LIGHT_BLUE);
    }


    /**
     * Log success
     *
     * @param $message
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function success($message, $type = 'SUCCESS')
    {
        return $this->writeLog($this->processLogTextTemplate($message, $type), FileColor::F_LIGHT_GREEN);
    }


    /**
     * Log write
     *
     * Write log message with given type and text color parameters in log file
     *
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
            $message = FileColor::getColoredString($message, $fColor, $bColor);
        }

        $expiredLogFile = $this->checkFileCreation();

        file_put_contents($this->logFilePath . '/' . $this->processFileTemplate($expiredLogFile), $message, FILE_APPEND);


        if ($this->saveLatestFileNumber) {
            /*check old logs and delete them*/
            $this->deleteOldLogs();

        }


        return $message;
    }


    /**
     * Constructor configuration
     *
     * Returns configuration object for constructor
     *
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
     * Log file template
     *
     * Returns template for log file with chosen configuration
     *
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
     * Log text template
     *
     * Returns template for log text with chosen configuration
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
            $files = glob("$logFilePath/*.$logFileName");
            $allFilesArray = [];

            foreach ($files as $key => $file) {
                $allFilesArray[$key]['time'] = filemtime($this->logFilePath . '/' . $file);
                $allFilesArray[$key]['name'] = $file;
            }

            usort($allFilesArray, function ($a, $b) {
                return $b['time'] - $a['time'];
            });

            if (count($allFilesArray) > $this->saveLatestFileNumber) {
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
     * File creation check
     *
     * Function checks if log file was created according FILE_CREATE_TYPE option
     *
     * Returns log file name or boolean(false)
     *
     * @return bool|mixed
     */
    private function checkFileCreation()
    {
        $logFileName = $this->getLatestLogFile();
        $logFilePath = $this->logFilePath . '/' . $logFileName;
        if ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_SIZE) {
            if (file_exists($logFilePath) && filesize($logFilePath) >= $this->filReCreateSize) {
                return $logFileName;
            }
        } elseif ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_TIME) {
            $lasElementInDir = count(scandir($this->logFilePath));
            $lastModifiedLogFileDate = strtotime(explode('_', scandir($this->logFilePath)[$lasElementInDir - 1])[0] . '+' . $this->fileReCreateDays . ' day');
            if (file_exists($logFilePath) && strtotime(date($this->logFileDateFormat)) >= $lastModifiedLogFileDate) {
                return $logFileName;
            }
        }

        return false;
    }

    /**
     * Get latest log
     *
     * Return latest log file from log directory
     *
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

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


    public $fileReCreateDays = 1;

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
            } elseif ($this->fileCreateType === self::FILE_CREATE_TYPE_BY_TIME) {
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


    /**
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
            // TODO get first create date from file
            $lasElementInDir = count(scandir($this->logFilePath));
            $lastModified = filemtime($logFilePath);
            $lastModifiedLogFileDate = strtotime(explode('_', scandir($this->logFilePath)[$lasElementInDir - 1])[0] . "+" . $this->fileReCreateDays . " day");
            if (file_exists($logFilePath) && $lastModified >= $lastModifiedLogFileDate) {
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
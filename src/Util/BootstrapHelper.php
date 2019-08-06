<?php

use Dotenv\Dotenv;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

class BootstrapHelper
{
    /**
     * Bootstrap env variables
     *
     * @return array
     * @throws Exception
     */
    public static function bootEnvironment()
    {
        $envPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config';

        $defaultEnv = new Dotenv($envPath, EnvConstants::ENV_FILE_DEFAULT);
        $defaultEnvRes = $defaultEnv->load();

        $envFile = $envPath . DIRECTORY_SEPARATOR . EnvConstants::ENV_FILE_CUSTOM;
        $envRes = [];
        if (file_exists($envFile)) {
            $env = new Dotenv($envPath, EnvConstants::ENV_FILE_CUSTOM);
            $envRes = $env->load();
        }

        $res = array_merge_recursive($defaultEnvRes, $envRes);

        return $res;
    }

    /**
     * Bootstrap translator
     *
     * @return Translator
     */
    public static function bootTranslator()
    {
        $translator = new Translator(getenv(EnvConstants::SITE_LANGUAGE), new MessageSelector());
        $translator->addLoader('yaml', new YamlFileLoader());

        $localeDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'locale';
        $locales = glob($localeDir . DIRECTORY_SEPARATOR . '*.yml');

        foreach ($locales as $locale) {
            $translator->addResource('yaml', $locale, basename($locale, '.yml'));
        }

        $translator->setFallbackLocales([getenv(EnvConstants::SITE_LANGUAGE), 'en']);

        return $translator;
    }

    /**
     * Bootstrap logger
     *
     * @return Logger
     * @throws Exception
     */
    public static function bootLogger()
    {
        $logName = getenv(EnvConstants::LOG_NAME);
        $logLevel = getenv(EnvConstants::LOG_LEVEL);

        switch ($logLevel) {
            case 'DEBUG':
                $logLevelTranslated = Logger::DEBUG;
                break;
            case 'INFO':
                $logLevelTranslated = Logger::INFO;
                break;
            case 'NOTICE':
                $logLevelTranslated = Logger::NOTICE;
                break;
            case 'WARNING':
                $logLevelTranslated = Logger::WARNING;
                break;
            case 'ERROR';
                $logLevelTranslated = Logger::ERROR;
                break;
            case 'CRITICAL':
                $logLevelTranslated = Logger::CRITICAL;
                break;
            case 'ALERT':
                $logLevelTranslated = Logger::ALERT;
                break;
            case 'EMERGENCY':
                $logLevelTranslated = Logger::EMERGENCY;
                break;
            default:
                $logLevelTranslated = Logger::DEBUG;
        }

        $logger = new Monolog\Logger($logName);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new ErrorLogHandler(NULL, $logLevelTranslated));

        $logPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'application.log';
        $logger->pushHandler(new StreamHandler($logPath, $logLevelTranslated));

        return $logger;
    }
}
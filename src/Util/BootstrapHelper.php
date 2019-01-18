<?php

use Dotenv\Dotenv;
use Monolog\Logger;
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
        $envFile = 'env';

        $exists = is_file($envPath . DIRECTORY_SEPARATOR . $envFile);
        if (!$exists) {
            die('Configure your environment in ' . $envPath . '.');
        } else {
            $env = new Dotenv($envPath, $envFile);
            $res = $env->load();

            try {
                $env->required([
                    'site_title',
                    'site_language',
                    'site_date_format',
                    'theme',
                    'theme_cache',
                    'teamspeak_default_host',
                    'teamspeak_default_query_port',
                    'teamspeak_default_user',
                    'log_name',
                    'log_level'
                ]);
            } catch (\Dotenv\Exception\ValidationException $e) {
                die($e->getMessage());
            }

            return $res;
        }
    }

    /**
     * Bootstrap translator
     *
     * @return Translator
     */
    public static function bootTranslator()
    {
        $translator = new Translator(getenv('site_language'), new MessageSelector());
        $translator->addLoader('yaml', new YamlFileLoader());

        $localeDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'locale';
        $locales = glob($localeDir . DIRECTORY_SEPARATOR . '*.yml');

        foreach ($locales as $locale) {
            $translator->addResource('yaml', $locale, basename($locale, '.yml'));
        }

        $translator->setFallbackLocales([getenv('site_language'), 'en']);

        return $translator;
    }

    /**
     * Bootstrap logger
     *
     * @return \Monolog\Logger
     * @throws Exception
     */
    public static function bootLogger()
    {
        $logName = getenv('log_name');
        $logLevel = getenv('log_level');

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
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler(NULL, $logLevelTranslated));

        $logPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'application.log';
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($logPath, $logLevelTranslated));

        return $logger;
    }
}
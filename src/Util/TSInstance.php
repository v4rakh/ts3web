<?php

use Psr\Log\LoggerInterface;

class TSInstance
{
    /**
     * @var ts3admin
     */
    private $ts;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $queryPort;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TeamSpeakWrapper constructor.
     * @param $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;

        $this->host = getenv(EnvConstants::TEAMSPEAK_HOST);
        $this->queryPort = getenv(EnvConstants::TEAMSPEAK_QUERY_PORT);

        $ts = new ts3admin($this->host, $this->queryPort);
        $ts = new TS3AdminProxy($ts, $logger);

        try {
            $ts->connect();
            $this->ts = $ts;
            $this->logger->debug(sprintf('Connected to %s:%s', $this->host, $this->queryPort));
        } catch (TSException $e) {
            die($e);
        }
    }

    /**
     * Login
     *
     * @param $user
     * @param $password
     * @return bool
     */
    public function login($user, $password)
    {
        if (!empty($user) && !empty($password)) {
            $this->ts->login($user, $password);
            $this->logger->debug(sprintf('Logged in as %s', $user));
        } else {
            throw new InvalidArgumentException('User and password not provided');
        }

        return true;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getQueryPort()
    {
        return $this->queryPort;
    }

    /**
     * @return ts3admin
     */
    public function getInstance()
    {
        return $this->ts;
    }

    /**
     * @return array
     */
    public static function getCodecs()
    {
        $arr = [];
        $arr['CODEC_SPEEX_NARROWBAND'] = ts3admin::CODEC_SPEEX_NARROWBAND;
        $arr['CODEC_SPEEX_WIDEBAND'] = ts3admin::CODEC_SPEEX_WIDEBAND;
        $arr['CODEC_SPEEX_ULTRAWIDEBAND'] = ts3admin::CODEC_SPEEX_ULTRAWIDEBAND;
        $arr['CODEC_CELT_MONO'] = ts3admin::CODEC_CELT_MONO;
        $arr['CODEC_OPUS_VOICE'] = ts3admin::CODEC_OPUS_VOICE;
        $arr['CODEC_OPUS_MUSIC'] = ts3admin::CODEC_OPUS_MUSIC;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getCodecsQuality()
    {
        $arr = [];
        $arr[1] = 1;
        $arr[2] = 2;
        $arr[3] = 3;
        $arr[4] = 4;
        $arr[5] = 5;
        $arr[6] = 6;
        $arr[7] = 7;
        $arr[8] = 8;
        $arr[9] = 19;
        $arr[10] = 10;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getHostMessageModes()
    {
        $arr = [];
        $arr['HostMessageMode_NONE'] = ts3admin::HostMessageMode_NONE;
        $arr['HostMessageMode_LOG'] = ts3admin::HostMessageMode_LOG;
        $arr['HostMessageMode_MODAL'] = ts3admin::HostMessageMode_MODAL;
        $arr['HostMessageMode_MODALQUIT'] = ts3admin::HostMessageMode_MODALQUIT;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getHostBannerModes()
    {
        $arr = [];
        $arr['HostBannerMode_NOADJUST'] = ts3admin::HostBannerMode_NOADJUST;
        $arr['HostBannerMode_IGNOREASPECT'] = ts3admin::HostBannerMode_IGNOREASPECT;
        $arr['HostBannerMode_KEEPASPECT'] = ts3admin::HostBannerMode_KEEPASPECT;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getCodecEncryptionModes()
    {
        $arr = [];
        $arr['CODEC_CRYPT_INDIVIDUAL'] = ts3admin::CODEC_CRYPT_INDIVIDUAL;
        $arr['CODEC_CRYPT_DISABLED'] = ts3admin::CODEC_CRYPT_DISABLED;
        $arr['CODEC_CRYPT_ENABLED'] = ts3admin::CODEC_CRYPT_ENABLED;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getLogLevels()
    {
        $arr = [];
        $arr['LogLevel_NONE'] = 0;
        $arr['LogLevel_ERROR'] = ts3admin::LogLevel_ERROR;
        $arr['LogLevel_WARNING'] = ts3admin::LogLevel_WARNING;
        $arr['LogLevel_DEBUG'] = ts3admin::LogLevel_DEBUG;
        $arr['LogLevel_INFO'] = ts3admin::LogLevel_INFO;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getTokenTypes()
    {
        $arr = [];
        $arr['TokenServerGroup'] = ts3admin::TokenServerGroup;
        $arr['TokenChannelGroup'] = ts3admin::TokenChannelGroup;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getPermGroupTypes()
    {
        $arr = [];
        $arr['PermGroupTypeServerGroup'] = ts3admin::PermGroupTypeServerGroup;
        $arr['PermGroupTypeGlobalClient'] = ts3admin::PermGroupTypeGlobalClient;
        $arr['PermGroupTypeChannel'] = ts3admin::PermGroupTypeChannel;
        $arr['PermGroupTypeChannelGroup'] = ts3admin::PermGroupTypeChannelGroup;
        $arr['PermGroupTypeChannelClient'] = ts3admin::PermGroupTypeChannelClient;

        return $arr;
    }

    /**
     * @return array
     */
    public static function getGroupTypes()
    {
        $arr = [];
        $arr['RegularGroup'] = 1;
        $arr['GlobalQueryGroup'] = 2;
        $arr['TemplateGroup'] = 0;

        return $arr;
    }
}
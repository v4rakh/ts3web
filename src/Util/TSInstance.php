<?php

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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * TeamSpeakWrapper constructor.
     * @param $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;

        $this->host = getenv('teamspeak_default_host');
        $this->queryPort = getenv('teamspeak_default_query_port');

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
}
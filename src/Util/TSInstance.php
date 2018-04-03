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
     * TeamSpeakWrapper constructor.
     */
    public function __construct()
    {
        $this->host = getenv('teamspeak_default_host');
        $this->queryPort = getenv('teamspeak_default_query_port');

        $ts = new ts3admin($this->host, $this->queryPort);

        if($ts->getElement('success', $ts->connect())) {
        } else {
            die('An unknown error occurred. A connection to the teamspeak server could not be established. Check settings.');
        }

        $this->ts = $ts;
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
            $res = $this->ts->login($user, $password);
            $this->checkCommandResult($res);
        } else {
            throw new InvalidArgumentException('User and password not provided');
        }

        return true;
    }

    /**
     * Check if any error occurred
     *
     * @param $result
     * @return bool
     */
    public function checkCommandResult($result)
    {
        if (!$this->ts->getElement('success', $result)) {

            $errors = $this->ts->getElement('errors', $result);
            $errorsAsString = implode('. ', $errors);

            if (count($errors) === 1) {

                // catch this
                if (strpos($errorsAsString, 'ErrorID: 1281 | Message: database empty result set') !== false) {
                    $throw = false;
                } else {
                    $throw = true;
                }
            } else {
                $throw = true;
            }

            if ($throw) {
                throw new TSException($errorsAsString);
            }
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
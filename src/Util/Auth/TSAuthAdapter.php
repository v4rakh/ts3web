<?php

use Psr\Log\LoggerInterface;
use Zend\Authentication\Result;

class TSAuthAdapter extends \Zend\Authentication\Adapter\AbstractAdapter
{
    /**
     * @var String
     */
    private $host;

    /**
     * @var Integer
     */
    private $queryPort;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TSInstance
     */
    private $ts;

    public function __construct($host, $queryPort, LoggerInterface $logger, TSInstance $ts)
    {
        $this->host = $host;
        $this->queryPort = $queryPort;
        $this->logger = $logger;
        $this->ts = $ts;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        $user = $this->getIdentity();
        $password = $this->getCredential();

        if ($this->ts->login($user, $password)) {
            $this->logger->debug(sprintf('Authenticated as %s', $user));

            $user = ['identity' => $user, 'user' => $user, 'password'=> $password, 'role' => ACL::ACL_DEFAULT_ROLE_ADMIN];
            return new Result(Result::SUCCESS, $user, array());
        } else {
            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                array(),
                array()
            );
        }
    }
}
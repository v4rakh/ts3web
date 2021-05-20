<?php

use Psr\Log\LoggerInterface;

class TS3AdminProxy
{
    private $object;
    private $logger;

    /**
     * TS3AdminProxy constructor.
     * @param ts3admin $object
     * @param $logger LoggerInterface
     */
    public function __construct(ts3admin $object, $logger)
    {
        $this->object = $object;
        $this->logger = $logger;
    }

    public function __call($method, $args)
    {

        // hide sensitive args
        if (in_array($method, ['login'])) {
            $this->logger->debug(sprintf('Calling %s', $method));
        } else {
            $this->logger->debug(sprintf('Calling %s', $method), $args);
        }

        // invoke original method
        $result = call_user_func_array(array($this->object, $method), $args);

        // check result
        if (is_array($result)) {
            $this->logger->debug('Received', $result);

            if (array_key_exists('success', $result)) {
                $success = call_user_func_array(array($this->object, 'getElement'), ['success', $result]);

                if (!$success) {
                    if (array_key_exists('errors', $result)) {
                        $errors = call_user_func_array(array($this->object, 'getElement'), ['errors', $result]);
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
                    } else {
                        throw new TSException('An unknown error occurred.');
                    }
                }
            }
        } else {
            $this->logger->debug('Received ' . $result);
        }

        return $result;
    }
}

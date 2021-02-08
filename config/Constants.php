<?php

/**
 * Class Constants
 */
class Constants
{
    /**
     * Years tag
     */
    const YEARS = '2020-2021';

    /**
     * Version tag
     */
    const VERSION = '2.2.3';

    /**
     * Return constant by it's class name
     *
     * @param $value
     * @return string|null
     * @throws ReflectionException
     */
    public static function get($value)
    {
        $constants = self::getConstants();
        if (!array_key_exists($value, $constants)) {
            return null;
        }

        return $constants[$value];
    }

    /**
     * Gets all constants
     *
     * @return array
     * @throws ReflectionException
     */
    private static function getConstants()
    {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
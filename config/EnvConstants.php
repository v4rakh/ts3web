<?php

/**
 * Class EnvConstants
 */
class EnvConstants
{
    /**
     * Example env file
     */
    const ENV_FILE_EXAMPLE = "env.example";

    /**
     * Custom env file
     */
    const ENV_FILE = "env";

    /**
     * Site title
     */
    const SITE_TITLE = "site_title";

    /**
     * Site language
     */
    const SITE_LANGUAGE = "site_language";

    /**
     * Site date format
     */
    const SITE_DATE_FORMAT = "site_date_format";

    /**
     * THeme
     */
    const THEME = "theme";

    /**
     * Cache
     */
    const THEME_CACHE = "theme_cache";

    /**
     * TeamSpeak host
     */
    const TEAMSPEAK_HOST = "teamspeak_host";

    /**
     * TeamSpeak query port
     */
    const TEAMSPEAK_QUERY_PORT = "teamspeak_query_port";

    /**
     * TeamSpeak default user
     */
    const TEAMSPEAK_USER = "teamspeak_user";

    /**
     * TeamSpeak log lines
     */
    const TEAMSPEAK_LOG_LINES = "teamspeak_log_lines";

    /**
     * Log name
     */
    const LOG_NAME = "log_name";

    /**
     * Log level
     */
    const LOG_LEVEL = "log_level";

    /**
     * Required attributes
     */
    const ENV_REQUIRED = [
        EnvConstants::SITE_TITLE,
        EnvConstants::SITE_LANGUAGE,
        EnvConstants::SITE_DATE_FORMAT,
        EnvConstants::THEME,
        EnvConstants::THEME_CACHE,
        EnvConstants::TEAMSPEAK_HOST,
        EnvConstants::TEAMSPEAK_QUERY_PORT,
        EnvConstants::TEAMSPEAK_USER,
        EnvConstants::TEAMSPEAK_LOG_LINES,
        EnvConstants::LOG_NAME,
        EnvConstants::LOG_LEVEL
    ];
}
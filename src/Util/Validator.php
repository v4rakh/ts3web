<?php

/**
 * Class Validator
 * @see Gump for use
 */
class Validator extends GUMP
{
    private $translator;

    public function __construct()
    {
        $this->translator = BootstrapHelper::bootTranslator();
    }

    /**
     * Perform data validation against the provided ruleset
     *
     * Arrays as FIELDS are added here as a custom feature
     *
     * @access public
     * @param mixed $input
     * @param array $ruleset
     * @return mixed
     * @throws \Exception
     */
    public function validate(array $input, array $ruleset)
    {
        $this->errors = [];

        foreach ($ruleset as $field => $rules) {
            #if(!array_key_exists($field, $input))
            #{
            #   continue;
            #}

            $rules = explode('|', $rules);

            if (in_array("required", $rules) || (isset($input[$field]) && (is_array($input[$field]) || trim($input[$field]) != ''))) {

                foreach ($rules as $rule) {
                    $method = NULL;
                    $param = NULL;

                    if (strstr($rule, ',') !== false) // has params
                    {
                        $rule = explode(',', $rule);
                        $method = 'validate_' . $rule[0];
                        $param = $rule[1];
                        $rule = $rule[0];
                    } else {
                        $method = 'validate_' . $rule;
                    }

                    // array required
                    if ($rule === "required" && !isset($input[$field])) {
                        $result = $this->$method($field, $input, $param);
                        $this->errors[] = $result;

                        return;
                    }

                    if (is_callable([$this, $method])) {
                        $result = $this->$method($field, $input, $param);

                        if (is_array($result)) // Validation Failed
                        {
                            $this->errors[] = $result;

                            return $this->errors;
                        }
                    } else {
                        if (isset(self::$validation_methods[$rule])) {
                            if (isset($input[$field])) {
                                $result = call_user_func(self::$validation_methods[$rule], $field, $input, $param);

                                $result = $this->$method($field, $input, $param);

                                if (is_array($result)) // Validation Failed
                                {
                                    $this->errors[] = $result;

                                    return $this->errors;
                                }
                            }
                        } else {
                            throw new \Exception("Validator method '$method' does not exist.");
                        }
                    }
                }
            }
        }

        return (count($this->errors) > 0) ? $this->errors : true;
    }

    public function filter_upper($value, $param = NULL)
    {
        return strtoupper($value);
    }

    public function filter_lower($value, $param = NULL)
    {
        return strtolower($value);
    }

    /**
     * Converts all error array into a single string
     * @return void
     */
    public function addErrorsToFlashMessage($flash)
    {
        $errors = $this->get_errors_array(true);

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $flash->addMessage('error', $error);
            }
        }
    }

    /**
     * Returns error array without HTML
     * @param null $convert_to_string
     * @return array|null
     */
    public function get_errors_array($convert_to_string = NULL)
    {
        if (empty($this->errors)) {
            return ($convert_to_string) ? NULL : [];
        }

        $resp = [];

        foreach ($this->errors as $e) {

            $field = ucwords(str_replace(['_', '-'], chr(32), $e['field']));
            $param = $e['param'];

            // Let's fetch explicit field names if they exist
            if (array_key_exists($e['field'], self::$fields)) {
                $field = self::$fields[$e['field']];
            }

            switch ($e['rule']) {
                case 'mismatch' :
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_required':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_valid_json_string':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_valid_email':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_max_len':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_min_len':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_exact_len':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_alpha':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_alpha_numeric':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_alpha_dash':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_numeric':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_integer':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_boolean':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_float':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_valid_url':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_url_exists':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_valid_ip':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_valid_cc':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_valid_name':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_contains':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => implode(', ', $param)]);
                    break;
                case 'validate_street_address':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_date':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_min_numeric':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_max_numeric':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_equals':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                case 'validate_set_min_len':
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $this->translator->trans('form_field_' . strtolower(str_replace([' '], '', $field))), '%param%' => $param]);
                    break;
                default:
                    $resp[$field] = $this->translator->trans($e['rule'], ['%field%' => $field, '%param%' => $param]);
            }
        }

        return $resp;
    }

    /**
     * @param array $input
     * @param array $fields
     * @param bool $utf8_encode
     * @return array
     */
    public function sanitize(array $input, array $fields = array(), $utf8_encode = true)
    {
        $magic_quotes = (bool)get_magic_quotes_gpc();

        if (empty($fields)) {
            $fields = array_keys($input);
        }

        $return = array();

        foreach ($fields as $field) {
            if (!isset($input[$field])) {
                continue;
            } else {
                $value = $input[$field];
                if (is_string($value)) {
                    if ($magic_quotes === true) {
                        $value = stripslashes($value);
                    }

                    if (strpos($value, "\r") !== false) {
                        $value = trim($value);
                    }

                    if (function_exists('iconv') && function_exists('mb_detect_encoding') && $utf8_encode) {
                        $current_encoding = mb_detect_encoding($value);

                        if ($current_encoding != 'UTF-8' && $current_encoding != 'UTF-16') {
                            $value = iconv($current_encoding, 'UTF-8', $value);
                        }
                    }

                    $value = strip_tags(trim(filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
                }

                $return[$field] = $value;
            }
        }

        return $return;
    }

    protected function validate_set_min_len($field, $input, $param = NULL)
    {

        $err = [
            'field' => $field,
            'value' => $input[$field],
            'rule' => __FUNCTION__,
            'param' => $param,
        ];

        if (!is_array($input[$field])) {
            return $err;
        }

        // default value
        if (empty($param)) $param = 1;

        if (count($input[$field]) < $param) return $err;

        return true;
    }

    /** Validates if $field content is equal to $param
     * @param $field
     * @param $input
     * @param $param
     * @return bool
     */
    protected function validate_equals($field, $input, $param)
    {
        $err = [
            'field' => $field,
            'value' => $input[$field],
            'rule' => __FUNCTION__,
            'param' => $param,
        ];

        if (!isset($input[$field]) || empty($input[$field]) || empty($param) || !isset($param)) {
            return $err;
        }

        if ($input[$field] != $param || $input[$field] !== $param) {
            return $err;
        }

        return true;
    }
}

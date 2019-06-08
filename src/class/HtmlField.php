<?php

class HtmlField
{
    /** Hidden */
    static function getHiddenfield(array $params) : string
    {
        $keys = array_keys($params);

        $html = "<input type='hidden'";
        foreach($keys as $key)
        {
            $html .= " " . $key . "='" . $params[$key] . "'";
        }
        $html .= ">";
        return $html;
    }

    /** Checkbox */
    static function getCheckbox(array $params, bool $isChecked = false, bool $isRequired = false) : string
    {
        $keys = array_keys($params);

        $html = "<input type='checkbox'";
        foreach($keys as $key)
        {
            $html .= " " . $key . "='" . $params[$key] . "'";
        }
        if ($isRequired)
        {
            $html .= " required";
        } 
        if ($isChecked)
        {
            $html .= " checked";
        } 
        $html .= ">";
        return $html;
    }

    /** Text area */
    static function getTextarea(array $params, bool $isRequired = false) : string
    {
        /* extract the value */
        $value = "";
        if (isset($params['value']))
        {
            $value = $params['value'];
            unset($params['value']);
        }
        $keys = array_keys($params);


        $html = "<textarea";
        foreach($keys as $key)
        {
            $html .= " " . $key . "='" . $params[$key] . "'";
        }
        $html .= ">" . $value . "</textarea>";
        return $html;
    }

    /** Text field */
    static function getTextfield(array $params) : string
    {
        $keys = array_keys($params);

        $html = "<input type='text'"; 
        foreach($keys as $key)
        {
            $html .= " " . $key . "='" . $params[$key] . "'";
        }
        $html .= ">";
        return $html;
    }
    
    /** Drop Down */
    static function getDropdown(array $params) : string
    {

    }

    /** Radio group */
    static function getRadioGroup(array $params) : string
    {

    }
    
    /** Email */
    static function getEmail(array $params) : string
    {

    }

    /** Password */
    static function getPassword(array $params) : string
    {

    }
}
?>
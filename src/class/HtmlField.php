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
        $params['value'] = 1;
        $attr = HtmlField::_getAttributes($params);

        $html = "<input type='checkbox' " . $attr;
        if ($isRequired)
        {
            $html .= " required";
        } 
        if ($isChecked)
        {
            $html .= " checked";
        } 
        $html .= ">";

		/* make sure there is an empty property uusing a value. */
		$params['value'] = 0;
		$attr = HtmlField::_getAttributes($params);

		$html = "<input type='hidden' " . $attr . " />\n" . $html;

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
    static function getDropdown(array $params, array $options) : string
    {
        $html = "<select";
        foreach ($params as $key => $value)
        {
            $html .= " {$key}='{$value}'";
        }

        $html .= ">\n";

        $keys = array_keys($options);

        foreach ($keys as $key)
        {
            $selected = ($key == $value ? " selected" : "");
            $html .= "  <option value='" . $key . "'{$selected}>" . $options[$key] . "</option>\n";
        }

        $html .= "</select>\n";
        return $html;

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

	private static function _getAttributes(array $params) : string 
	{
		$attr = "";
		$keys = array_keys($params);
		
        foreach($keys as $key)
        {
            $attr .= (empty($attr)?"":" ") . $key . "='" . $params[$key] . "'";
        }	
		return $attr;
	}
}
?>
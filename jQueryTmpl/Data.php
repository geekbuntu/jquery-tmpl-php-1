<?php

class jQueryTmpl_Data
{
    private $_data;

    public function __construct(stdClass $data)
    {
        // The json_decode() returned stdObject
        $this->_data = $data;
    }

    public function getValueOf($jsNotation)
    {
        $dataTokens = $this->_parseJsNotation($jsNotation);

        $localData = $this->_data;

        foreach ($dataTokens as $token)
        {
            $localData = $this->_getDataPart($localData, $token);
        }

        return $localData;
    }

    private function _parseJsNotation($js)
    {
        $dataTokens = array();

        // Split based on '.' to traverse into object. Could be smarter...
        $loc = explode('.', $js);

        foreach($loc as $idx => $name)
        {
            $match = array();

            if (!preg_match('/^([a-z_$][0-9a-z_$]*)(\[.*\])?$/i', $name, $match))
            {
                throw new jQueryTmpl_Data_Exception('jQueryTmpl_Data can not evaluate expressions.');
            }

            $dataTokens[] = $match[1];

            if (!empty($match[2]))
            {
                $subparts = explode('][', $match[2]);

                foreach ($subparts as $subname)
                {
                    $dataTokens[] = trim($subname, ' \'"[]');
                }
            }
        }

        return $dataTokens;
    }

    private function _getDataPart($data, $token)
    {
        if ($data instanceof stdClass)
        {
            return $this->_getDataPartObject($data, $token);
        }

        if (is_array($data))
        {
            return $this->_getDataPartArray($data, $token);
        }

        if (is_string($data))
        {
            return $this->_getDataPartString($data, $token);
        }

        // At this point we are told to go into a primitive type, this is undefined
        return '';
    }

    private function _getDataPartObject(stdClass $data, $token)
    {
        if ($token == 'length')
        {
            $i = 0;

            foreach ($data as $element)
            {
                $i++;
            }

            return $i;
        }

        return $data->$token;
    }

    private function _getDataPartArray(array $data, $token)
    {
        if ($token == 'length')
        {
            return count($data);
        }

        if (!$this->_isInt($token))
        {
            return '';
        }

        return $data[$token];
    }

    private function _getDataPartString($data, $token)
    {
        if ($token == 'length')
        {
            return strlen($data);
        }

        if (!$this->_isInt($token))
        {
            return '';
        }

        return $data[$token];
    }

    private function _isInt($var)
    {
        return ((int)$var == $var);
    }
}


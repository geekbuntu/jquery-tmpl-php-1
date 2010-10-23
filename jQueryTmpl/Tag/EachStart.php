<?php

class jQueryTmpl_Tag_EachStart implements jQueryTmpl_Tag
{
    public function getTokenType()
    {
        return 'EachStart';
    }

    public function getRegex()
    {
        return '/{{each.*?}}/is';
    }

    public function parseTag($rawTagString)
    {
        $matches = array();
        preg_match('/^{{each(\((.*?),(.*)\)(.*)|(.*))}}$/is', $rawTagString, $matches);

        if (trim($matches[3]) == '')
        {
            return array
            (
                'name' => trim($matches[1])
            );
        }

        // Matched optional params as well
        return array
        (
            'name' => trim($matches[4]),
            'index' => trim($matches[2]),
            'value' => trim($matches[3])
        );
    }
}


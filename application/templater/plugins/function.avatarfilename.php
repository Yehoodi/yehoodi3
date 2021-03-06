<?php
    function smarty_function_avatarfilename($params, $smarty)
    {
        if (!isset($params['id']))
            $params['id'] = 0;

        if (!isset($params['w']))
            $params['w'] = 14;

        if (!isset($params['h']))
            $params['h'] = 1970;

        require_once $smarty->_get_plugin_filepath('function', 'geturl');

        $hash = DatabaseObject_ResourceImage::GetImageHash(
            $params['id'],
            $params['w'],
            $params['h']
        );

        $options = array(
            'controller' => 'utility',
            'action'     => 'avatar'
        );

        return sprintf(
            '%s?id=%d&amp;w=%d&amp;h=%d&amp;hash=%s',
            smarty_function_geturl($options, $smarty),
            $params['id'],
            $params['w'],
            $params['h'],
            $hash
        );
    }
?>
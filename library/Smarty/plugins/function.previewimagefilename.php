<?php
    function smarty_function_previewimagefilename($params, $smarty)
    {
        if (!isset($params['tempFilename']))
            $params['tempFilename'] = 0;

        if (!isset($params['w']))
            $params['w'] = 0;

        if (!isset($params['h']))
            $params['h'] = 0;

        require_once $smarty->_get_plugin_filepath('function', 'geturl');

/*        $hash = DatabaseObject_ResourceImage::GetImageHash(
            $params['id'],
            $params['w'],
            $params['h']
        );
*/
        $options = array(
            'controller' => 'utility',
            'action'     => 'imagepreview'
        );

        return sprintf(
            '%s?tempFilename=%s&amp;w=%d&amp;h=%d',
            smarty_function_geturl($options, $smarty),
            $params['tempFilename'],
            $params['w'],
            $params['h']
        );
    }
?>
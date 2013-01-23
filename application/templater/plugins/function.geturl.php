<?php
    function smarty_function_geturl($params, $smarty)
    {
        //Zend_Debug::dump($params);
        $action     = isset($params['action']) ? $params['action'] : null;
        $controller = isset($params['controller']) ? $params['controller'] : null;

        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');

        $request = Zend_Controller_Front::getInstance()->getRequest();

        //$url  = rtrim($request->getBaseUrl(), '/') . '/';
        $url  = rtrim($request->getBaseUrl(), '/');
        $url .= $helper->simple($action, $controller);

        //Zend_Debug::dump($request);die;
        return '/' .ltrim($url, '/');
    }
?>
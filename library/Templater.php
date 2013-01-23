<?php
    class Templater extends Zend_View_Abstract
    {
        protected $_path;
        protected $_engine;

        public function __construct()
        {
            $config = Zend_Registry::get('smartyConfig');

            require_once('Smarty/Smarty.class.php');

            $this->_engine = new Smarty();
            $this->_engine->template_dir = $config->paths->templates;
            $this->_engine->compile_dir = sprintf('%s'.DIRECTORY_SEPARATOR.'templates_c',
                                                  $config->paths->data);

            $this->_engine->plugins_dir = array($config->paths->base .
                                                DIRECTORY_SEPARATOR . 'application' 
                                                . DIRECTORY_SEPARATOR . 'templater' 
                                                . DIRECTORY_SEPARATOR.'plugins',
                                                'plugins');
                                                
            if ($config->debugging || isset($_GET['sd']) && Zend_Registry::get('serverConfig')->env != 'production')
            {
               $this->_engine->debugging = true;
            }
        }

        public function getEngine()
        {
            return $this->_engine;
        }

        public function __set($key, $val)
        {
            $this->_engine->assign($key, $val);
        }

        public function __get($key)
        {
            return $this->_engine->get_template_vars($key);
        }

        public function __isset($key)
        {
            return $this->_engine->get_template_vars($key) !== null;
        }

        public function __unset($key)
        {
            $this->_engine->clear_assign($key);
        }

        public function assign($spec, $value = null)
        {
            if (is_array($spec)) {
                $this->_engine->assign($spec);
                return;
            }

            $this->_engine->assign($spec, $value);
        }

        public function clearVars()
        {
            $this->_engine->clear_all_assign();
        }

        public function render($name)
        {
            // change this back to fetch for the old style method or display for the new.
            // fetch works for emails and such. Production needs fetch!
            // display works for debugging.

            // Debugging? Smarty and PQP switch
			if (Zend_Registry::get('smartyConfig')->debugging == true) {
	            return $this->_engine->display(strtolower($name));
			}
            return $this->_engine->fetch(strtolower($name));
        }

        public function fetch($name)
        {
            // test access method to get to the smarty fetch method.
        	return $this->_engine->fetch(strtolower($name));
        }

        public function _run()
        { }
    }
?>
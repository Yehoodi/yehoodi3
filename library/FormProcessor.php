<?php
    abstract class FormProcessor
    {
        protected $_errors = array();
        protected $_vals = array();
        private $_sanitizeChain = null;

        public function __construct()
        {

        }

        abstract function process(Zend_Controller_Request_Abstract $request);

        public function sanitize($value)
        {
            if (!$this->_sanitizeChain instanceof Zend_Filter) {
                $this->_sanitizeChain = new Zend_Filter();
                $this->_sanitizeChain->addFilter(new Zend_Filter_StringTrim())
                                     ->addFilter(new Zend_Filter_StripTags());
            }

            // filter out any line feeds / carriage returns
            $ret = preg_replace('/[\r\n]+/', ' ', $value);

            // filter using the above chain
            return $this->_sanitizeChain->filter($ret);
        }

        /**
         * Enter description here...
         *
         * @param unknown_type $string
         * @param unknown_type $allowtags
         * @param unknown_type $allowattributes
         * @return unknown
         */
        protected function strip_tags_attributes($string, $allowtags=NULL, $allowattributes=NULL) { 
            $string = strip_tags($string,$allowtags); 
            if (!is_null($allowattributes)) { 
                if(!is_array($allowattributes)) 
                    $allowattributes = explode(",",$allowattributes); 
                if(is_array($allowattributes)) 
                    $allowattributes = implode(")(?<!",$allowattributes); 
                if (strlen($allowattributes) > 0) 
                    $allowattributes = "(?<!".$allowattributes.")"; 
                $string = preg_replace_callback("/<[^>]*>/i",create_function( 
                    '$matches', 
                    'return preg_replace("/ [^ =]*'.$allowattributes.'=(\"[^\"]*\"|\'[^\']*\')/i", "", $matches[0]);'    
                ),$string); 
            } 
            return $string; 
        }
        
        
        /**
         * Cleans up the html with the allowed tags in the array
         * at the top of this class
         *
         * @param unknown_type $html
         * @return unknown
         */
        protected function cleanHtml($html, $tags)
        {
            
//            $chain = new Zend_Filter();
//            $chain->addFilter(new Zend_Filter_StripTags($tags));
//            $chain->addFilter(new Zend_Filter_StringTrim());
//
//            $html = $chain->filter($html);

            // Hack for the forced URLs to work
            $html = str_replace('<http://','&lt;http://',$html);
            
            $html = $this->strip_tags_attributes($html, $tags, 'href,target,name,title,src,alt,width,height,align');
            
            //Zend_Debug::dump($html);die;
            $tmp = $html;
            while (1) {
                // Try and replace an occurrence of javascript:
                $html = preg_replace('/(<[^>]*)javascript:([^>]*>)/i',
                                     '$1$2',
                                     $html);

                // If nothing changed this iteration then break the loop
                if ($html == $tmp)
                    break;

                $tmp = $html;
            }

            // This handles the weird line break behavior on a post
            $html = preg_replace("|[\n\r][\n\r]|", "\t", $html);
			$html = preg_replace("|\t|", "\n", $html);
            
			//Zend_Debug::dump($html);die;
			return $html;
        }

        public function addError($key, $val)
        {
        	if (array_key_exists($key, $this->_errors)) {
                if (!is_array($this->_errors[$key]))
                    $this->_errors[$key] = array($this->_errors[$key]);

                $this->_errors[$key][] = $val;
            }
            else
                $this->_errors[$key] = $val;
        }

        public function getError($key)
        {
            if ($this->hasError($key))
                return $this->_errors[$key];

            return null;
        }

        public function getErrors()
        {
            return $this->_errors;
        }

        public function hasError($key = null)
        {
            if (strlen($key) == 0)
                return count($this->_errors) > 0;

            return array_key_exists($key, $this->_errors);
        }

        public function __set($name, $value)
        {
            $this->_vals[$name] = $value;
        }

        public function __get($name)
        {
            return array_key_exists($name, $this->_vals) ? $this->_vals[$name] : null;
        }
    }
?>
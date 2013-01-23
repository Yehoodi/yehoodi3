<?php
    class Breadcrumbs
    {
        // breadcrumb object property is an array
    	private $_trail = array();

        /**
         * adds a step to the $_trail array using $title and $link
         *
         * @param string $title
         * @param string $link
         */
    	public function addStep($title, $link = '')
        {
            $this->_trail[] = array('title' => $title,
                                    'link'  => $link);
        } // addStep()

        /**
         * get the current $_trail
         *
         * @return array
         */
        public function getTrail()
        {
            return $this->_trail;
        } // getTrail()

        /**
         * returns just the title of the last link in the trail or null
         * this is mostly for the page <title>
         *
         * @return string (the title)
         */
        public function getTitle()
        {
            if (count($this->_trail) == 0)
                return null;

            return $this->_trail[count($this->_trail) - 1]['title'] . ' - Yehoodi.com';
        } // getTitle()
    }
?>
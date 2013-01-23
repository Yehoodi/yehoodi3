<?php

/**
 * Static Frim Fram Jam Page
 * for now
 *
 */
class FrimframController Extends CustomControllerAction
{
    public function indexAction()
    {
        // Get the current extraFramInfo
        $info = DatabaseObject_Resource::getExtraFramInfo($this->db);
        
        $this->view->extraFramInfo = $info;
    }
}
<?php

/**
 * Yehoodi 3.0 DiscussionAjaxController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class DiscussionajaxController extends CustomControllerAction 
{
	public $Id;
	protected $ajaxPass;

	public function init()
	{
        parent::init();
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

        // Create ajax password
        $this->ajaxPass = PasswordManager::getInstance();
        $this->ajaxPass->createPassword();
	} // init

	
	/**
	 * Ajax method for toggling a
	 * bookmarked resource
	 *
	 */
	public function bookmarkAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest() ) {
		
			$resourceId = (int) str_replace('a_bookmark_','',$this->_request->getParam('rsrc_id'));
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('rsrc_id'	=>	$resourceId,
	        				 'user_id'	=>	$user_id
	        			);
	        
	        //Zend_Debug::dump($options);die;
	        			
	        if ($bookmarkId = DatabaseObject_UserBookmark::getBookmark($this->db, $options)) {
	        	// We got a bookmark, so we must delete it
	        	$bookmark = new DatabaseObject_UserBookmark($this->db);
	        	$bookmark->load($bookmarkId);
	        	$bookmark->delete();
	        	
	        	$bookmarked = "false";
	        } else {
	        	// Nothing was returned so it's a new bookmark
	        	$bookmark = new DatabaseObject_UserBookmark($this->db);
	        	$bookmark->user_id = $user_id;
	        	$bookmark->rsrc_id = $resourceId;
	        	$bookmark->save();
	
	        	$bookmarked = "true";
	        }
	        
	        // Clear the user's bookmarks cache keys
	        DatabaseObject_UserBookmark::updateBookmarkCache($user_id);
	        
	        $output = $bookmarked;
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Ajax method for toggling a
	 * voted resource
	 *
	 */
	public function voteAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest()) {

			$resourceId = (int) str_replace('a_vote_','',$this->_request->getParam('rsrc_id'));
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('rsrc_id'	=>	$resourceId,
	        				 'user_id'	=>	$user_id
	        			);
	        
	        if ($voteId = DatabaseObject_UserVote::getVote($this->db, $options)) {
	        	// We got a vote, so the user wants to get rid of it
	        	$vote = new DatabaseObject_UserVote($this->db);
	        	$vote->load($voteId);
	        	$vote->delete();
	        	
	        	$voted = "false";
	        } else {
	        	// Nothing was returned so it's a new vote
	        	$vote = new DatabaseObject_UserVote($this->db);
	        	$vote->user_id = $user_id;
	        	$vote->rsrc_id = $resourceId;
	        	$vote->save();
	        	
	        	$voted = "true";
	        }
	        
	        // Update the resource vote count for sorting
	        DatabaseObject_UserVote::updateResourceVoteCount($this->db, $resourceId);
	        
	        $output = $voted;
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Ajax method for toggling a
	 * watched (notify) resource
	 *
	 */
	public function notifyAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest()) {
		
			$resourceId = (int) str_replace('a_notify_','',$this->_request->getParam('rsrc_id'));
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('rsrc_id'	=>	$resourceId,
	        				 'user_id'	=>	$user_id
	        			);
	        
	        if ($notifyId = DatabaseObject_UserResourceNotify::getNotify($this->db, $options)) {
	        	// We got a notify, so we must delete it
	        	$notify = new DatabaseObject_UserResourceNotify($this->db);
	        	$notify->load($notifyId);
	        	$notify->delete();
	        	
	        	$notified = "false";
	        } else {
	        	// Nothing was returned so it's a new notify
	        	$notify = new DatabaseObject_UserResourceNotify($this->db);
	        	$notify->user_id = $user_id;
	        	$notify->rsrc_id = $resourceId;
	        	$notify->notify_status = DatabaseObject_UserResourceNotify::NOTIFIED_NO ;
	        	$notify->save();
	
	        	$notified = "true";
	        }
	        
	        $output = $notified;
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Ajax method for reporting to mods
	 *
	 * This echoes "true" if the report was new and has been reported
	 * 
	 * "false" if the report for this resource has already gone out.
	 */
	public function reportAction()
	{
		if($this->identity > 0 && $this->_request->isXmlHttpRequest()) {
		
			$resourceId = (int) str_replace('a_report_','',$this->_request->getParam('rsrc_id'));
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('rsrc_id'	=>	$resourceId,
	        				 'user_id'	=>	$user_id
	        			);
	        
	        if ($reportId = DatabaseObject_ResourceReport::getReport($this->db, $options)) {
	        	// We got a report. Don't do anything.
	        	//$report = new DatabaseObject_UserResourcereport($this->db);
	        	//$report->load($reportId);
	        	//$report->delete();	
	        	
	        	$reported = "false";
	        } else {
	        	// Nothing was returned so it's a new report
	        	$report = new DatabaseObject_ResourceReport($this->db);
	        	$report->user_id = $user_id;
	        	$report->rsrc_id = $resourceId;
	        	$report->report_status = DatabaseObject_ResourceReport::NOTIFIED_NO;
	        	$report->save();
	        	
	        	$reporterId = array('user_id' => $user_id
	        				  );
	        	
	        	// Send the notifications
				$mail = new Notifier($this->db, $reporterId);
				$mail->sendSpamReportNotification('mod-spam-report.tpl', $resourceId);

				$reported = "true";
	        }
	        
	        $output = $reported;
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Ajax method for toggling a
	 * calendar resource
	 *
	 */
	public function calendarAction()
	{
		if($this->identity > 0) {

			$resourceId = (int) str_replace('a_calendar_','',$this->_request->getParam('rsrc_id'));
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('rsrc_id'	=>	$resourceId,
	        				 'user_id'	=>	$user_id
	        			);
	        
	        if ($calendarId = DatabaseObject_UserCalendar::getCalendar($this->db, $options)) {
	        	// We got a calendar entry, so the user wants to get rid of it
	        	$calendar = new DatabaseObject_UserCalendar($this->db);
	        	$calendar->load($calendarId);
	        	$calendar->delete();
	        	
	        	$calendard = "false";
	        } else {
	        	// Nothing was returned so it's a new calendar
	        	$calendar = new DatabaseObject_Usercalendar($this->db);
	        	$calendar->user_id = $user_id;
	        	$calendar->rsrc_id = $resourceId;
	        	$calendar->save();
	        	
	        	$calendard = "true";
	        }
	        
	        $output = $calendard;
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
     * Simple get the name of the resource type
     * from the category url. This is for the 
     * discussion bar.
	 *
	 * @return string
	 */
    public function getresourcenameAction()
    {
		//if($this->identity > 0) {

			$cat_site_url = $this->_request->getParam('cat_site_url');
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	        // select options
	        $options = array('rsrc_type_id' => (int) $select_resourceTypeId
	        			);
	        
	        $output = DatabaseObject_Resource::getResourceNameByCategoryUrl($this->db, array('cat_site_url' => $cat_site_url));
	        
	        if ($output == "") {
	        	$output = "all";
	        }
	
	        echo $output;
		//} else {
		//	die("Access Denied");
		//}
    }

	/**
     * Simple get the id of the resource type
     * from the category url. This is for the 
     * discussion bar.
	 *
	 * @return string
	 */
    public function getresourceidAction()
    {
		//if($this->identity > 0) {

			$cat_site_url = $this->_request->getParam('cat_site_url');
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	        // select options
	        $options = array('rsrc_type_id' => (int) $select_resourceTypeId
	        			);
	        
	        $output = DatabaseObject_Resource::getResourceIdByCategoryUrl($this->db, array('cat_site_url' => $cat_site_url));
	        
	        echo $output;
		//} else {
		//	die("Access Denied");
		//}
    }

    public function savediscussionbarAction()
    {
		if($this->identity > 0) {

			$templater = new Templater();
			$tpl = 'discussion-bar_ajax.tpl';
			$discussionBarUrl = $this->_request->getParam('url');
	
			$user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// Attempt to save the discussionbar url to the user's profile
	    	$user = new DatabaseObject_User($this->db);
	    	$user->load($user_id);
	
	    	if($user->profile->browse_bar) {
				// We got a discussion bar setting so we must delete it
	    		$user->profile->browse_bar = "";
				$user->save();
				
				$templater->alt = "Save Discussion bar settings";
	    	} else {
				// Brand new discussion bar setting
	    		$user->profile->browse_bar = $discussionBarUrl;
				$user->save();
	
				$templater->alt = "Reset the Discussion bar";
	    	}
	
	    	// fetch the bookmark template output
	        $output = $templater->render('discussion/' . $tpl);
	
	        echo $output;
 		} else {
			die("Access Denied");
		}
   }
   
    /**
     * Refresh the discussion page category list
     * with the appropriate categories based on
     * the chosen resource type id via ajax
	 *
	 * @param smarty template $tpl
	 * @param int $select_resourceTypeId
	 * @return html
	 */
    public function ajaxselectcategoryAction()
    {

		$templater = new Templater();
		$tpl = 'ajax-discussion_bar-category-list.tpl';
		$select_resourceTypeId = $this->_request->getParam('select_resourceTypeId');

		// Turns off automatic rendering to the template
		$this->_helper->viewRenderer->setNoRender();
        
        // select options
        $options = array('rsrc_type_id' => (int) $select_resourceTypeId,
        				 'order'		=> 'c.order'
        			);
        
        $templater->categoryTypes = DatabaseObject_Category::getCategories($this->db, $options);

        // fetch the category list output
        $output = $templater->render('lib/' . $tpl);
        
        echo $output;
    }

	/**
     * Refresh the discussion page sory by list
     * with the appropriate sorts based on
     * the chosen resource type id via ajax
	 *
	 * @param smarty template $tpl
	 * @param int $select_resourceTypeId
	 * @return html
	 */
    public function ajaxselectsortbyAction()
    {

		$templater = new Templater();
		$select_resourceTypeId = $this->_request->getParam('select_resourceTypeId');
		$event = 3;

		// Turns off automatic rendering to the template
		$this->_helper->viewRenderer->setNoRender();

		switch ($select_resourceTypeId) {
			case $event:
				$tpl = 'ajax-discussion_bar-sort-list_event.tpl';
				break;
			
			default:
				$tpl = 'ajax-discussion_bar-sort-list.tpl';
				break;
		}

        // fetch the sort by list output
        $output = $templater->render('lib/' . $tpl);
        
        echo $output;
    }
    
}
<?php
class ErrorController extends CustomControllerAction 
{
    public function errorAction()
    {
		$request = $this->getRequest();
		
		$controller = $request->getParam('controller');
		$action = $request->getParam('action');
    	
    	// Regular Redirects
    	if ($controller == 'nyc') {
    	    // Redirect to New York Calendar
			$this->_redirect("http://www.yehoodi.com/calendar/month/all/?location=other&lon=-74.0059731&lat=40.7143528&loc=New%20York,%20NY,%20USA");
    	} elseif ($controller == 'sf') {
			$this->_redirect("http://www.yehoodi.com/calendar/month/all/?location=other&lon=-122.4194155&lat=37.7749295&loc=San%20Francisco,%20CA,%20USA");
    	}
		
		// Old Yehoodi Redirects Page
		if($controller == 'phpBB2') {
    		switch ($request->getParam('action'))
    		{
    			// redirect old /phpBB2/viewtopic.php?t=xxxxx
    			case 'viewtopic.php':
    				$rsrc_id = $request->getParam('t');
    				$comment_id = $request->getParam('p');
    				
    				if($rsrc_id > 0) {
        				$seo = DatabaseObject_Resource::getResourceUrl($this->db, $rsrc_id);
        				
        				$this->_redirect("/comment/" . $rsrc_id . "/" . $seo . "/");
    				} elseif ($comment_id > 0) {
        				$url = DatabaseObject_Resource::getResourceUrlByCommentId($this->db, $comment_id);
    					
        				// Get the current page number
        				$currentPage = ceil($url[0]['comment_num'] / Zend_Registry::get('paginationConfig')->CommentsPerPage);
        				
        				$this->_redirect('/comment/' . $url[0]['rsrc_id'] . '/' . $url[0]['rsrc_url'] . '/' . $currentPage . '#comment_' . $url[0]['comment_num']);
    				} else {
        				$this->_redirect("http://www.yehoodi.com/");
    				}
    				break;
        
    			// redirect old /phpBB2/viewforum.php?f=x
    			case 'viewforum.php':
    				$rsrc_id = $request->getParam('f');
    				$this->_redirect("http://www.yehoodi.com/discussion");
    				break;
    
    			// redirect old /phpBB2/viewactive.php
    			case 'viewactive.php':
    				$this->_redirect("http://www.yehoodi.com/discussion/all/all/30days/activity/");
    				break;
    
    		  // redirect old /phpBB2/index.php
    			case 'index.php':
    				$this->_redirect("http://www.yehoodi.com/discussion");
    				break;
    		}
		} elseif ($controller == 'mrjesse') {
    		
		    //Zend_Debug::dump($request->getParams());die;
		    switch ($request->getParam('action'))
    		{
    		    // The old URL for the heymisterjesse.xml
    		    case 'rss.xml':
    		        $this->_redirect('http://www.yehoodi.com/rss/heymisterjesse.xml');
    		        break;

    		    // The URL for Hey mister jesse landing page
    		    default:
    		        $this->_redirect('http://www.yehoodi.com/show/heymisterjesse');
    		        break;
    		}
		    
		} elseif ($controller == 'talk') {
    		
		    //Zend_Debug::dump($request->getParams());die;
		    switch ($request->getParam('action'))
    		{
    		    // The old URL for the yehoodi talk show .xml
    		    case 'vidrss.xml':
    		        $this->_redirect('http://www.yehoodi.com/rss/yehoodivideo.xml');
    		        break;

    		    case 'rss.xml':
    		        $this->_redirect('http://www.yehoodi.com/rss/yehooditalkshow.xml');
    		        break;
    		        
    		    default:
    		        $this->_redirect('http://www.yehoodi.com/show/yehooditalkshow');
    		}
		    
		} elseif ($controller == 'news') {
    		
	        $this->_redirect('http://www.yehoodi.com/discussion/featured/all/date/?view=normal');
		    
		} elseif ($controller == 'radio') {
    		
	        $this->_redirect('http://www.yehoodi.com/show/radio');
		    
		} elseif ($controller == 'browse') {
    		
	        $this->_redirect(Zend_Registry::get('serverConfig')->location . 'discussion/');
		    
		} elseif ($controller == 'jira') {
    		
	        $this->_redirect(Zend_Registry::get('serverConfig')->jiraURL);
		    
		}
    }
}
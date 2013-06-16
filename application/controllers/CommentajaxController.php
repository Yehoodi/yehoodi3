<?php

/**
 * Yehoodi 3.0 CommentAjaxController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class CommentajaxController extends CustomControllerAction 
{
	public $commentId;
    public $identity;
	const LASTCOMMENT_CACHE_KEY_TIME = 3600; // 1 hour

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
	 * Responsible for getting the
	 * username and excerpt of the
	 * user in the comment reply box
	 *
	 */
	public function showreplyboxAction()
	{
		//if($this->ajaxPass->verifyPassword() && $this->identity > 0) {
		if($this->identity && $this->_request->isXmlHttpRequest()) {
		
	        $templater = new Templater();
			$tpl = 'comment-reply-box.tpl';
			$replyToCommentId = (int) $this->_request->getParam('comment_id');
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	    	// select options
	        $options = array('comment_id' => (int) $replyToCommentId
	        			);
	        
	        $templater->comment = DatabaseObject_Comment::getCommentById($this->db, $options);
			$templater->nextCommentNum = DatabaseObject_Comment::getNextCommentCount($this->db, array('rsrc_id' => $templater->comment[rsrc_id]));
			
			// fetch the category list output
	        $output = $templater->render('comment/' . $tpl);
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Responsible for getting the
	 * username and excerpt of the
	 * user in the comment reply box
	 *
	 */
	public function getwmdjavascriptAction()
	{
		if($this->identity && $this->_request->isXmlHttpRequest()) {
		
	        $templater = new Templater();
			$tpl = 'module.wmd-javascript.tpl';
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
			// fetch
	        $output = $templater->render('modules/' . $tpl);
	        
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	public function updatereplyusernameAction()
	{
		if($this->identity && $this->_request->isXmlHttpRequest()) {
	        
	        $templater = new Templater();
			$tpl = 'comment-original-author.tpl';
	
			$params = explode('_',$this->_request->getParam('id'));
			
			$type = $params[0]; // r for resource, c for comment
			$id = $params[1]; // the id of the resource or comment
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	        if ($type == 'c') {
				// We are pulling the comment author
		        $options = array('comment_id' => (int) $id
		        			);
	        	$text = DatabaseObject_Comment::getUserNameByCommentId($this->db, $options);
		    	$author = $text;
	        } elseif ($type == 'r') {
	        	// Get the author from the original resource (the first post)
	        	$options = array('rsrc_id' => (int) $id
	        				);
	        	$text = DatabaseObject_Resource::getUserNameByResourceId($this->db, $options);
		    	$author = $text;
	        }
	
	        // Strip the html tags since we don't need them
	        $templater->originalAuthor = strip_tags(htmlspecialchars_decode($author));
	
	        // Send text through the Smarty template
	        $output = $templater->render('comment/' . $tpl);
	
	        // Output it to screen
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Updates the Add New Comment Excerpt area
	 *
	 */
	public function updatereplycommentAction()
	{
		if($this->identity && $this->_request->isXmlHttpRequest()) {
	        
	        $templater = new Templater();
			$tpl = 'comment-excerpt.tpl';
	
			$params = explode('_',$this->_request->getParam('id'));
			
			$type = $params[0]; // r for resource, c for comment
			$id = $params[1]; // the id of the resource or comment
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
	        if ($type == 'c') {
				// We are pulling the comment excerpt
		        $options = array('comment_id' => (int) $id
		        			);
	        	$text = DatabaseObject_Comment::getCommentTextByCommentId($this->db, $options);
		    	$comment = $text['comment'];
	        } elseif ($type == 'r') {
	        	// Get the body text from the original resource (the first post)
	        	$options = array('rsrc_id' => (int) $id
	        				);
	        	$text = DatabaseObject_Resource::getResourceTextByResourceId($this->db, $options);
	        	$comment = $text['descrip'];
	        }
	
	        // Kill any non alphanumeric character
	        $comment = preg_replace("/[^a-zA-Z0-9\s\p{P}]/","",$comment);
	        
	        // Strip the html tags since we don't need them
			
	        $templater->excerpt = strip_tags(htmlspecialchars_decode($comment));
	        
	    	// Get current user_id if any
	        $auth = Zend_Auth::getInstance();
	    	if ($auth->hasIdentity()) {
	    		// User has dirty words filtered?
	    		$filterDirty = DatabaseObject_User::checkDirtyFilter($this->db, $auth->getIdentity()->user_id);
	    		if($filterDirty != 'off') {
					$templater->excerpt = UtilityController::cleanDirtyWords($templater->excerpt);
	    		}
	    	}
	
	        // Send text through the Smarty template
	        $output = $templater->render('comment/' . $tpl);
	
	        // Output it to screen
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	/**
	 * Updates the quoted comment area
	 *
	 */
	public function updatequotedcommentAction()
	{
		if($this->identity && $this->_request->isXmlHttpRequest()) {
	        
	        $templater = new Templater();
			$tpl = 'comment-quote.tpl';
	
			$params = explode('_',$this->_request->getParam('id'));
			
			$type = $params[0]; // c for quoted comment
			$id = $params[1]; // the id of the resource or comment
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
			// We are pulling the full quoted comment
	        $options = array('comment_id' => (int) $id
	        			);
	    	// get the quoted comment's username
	    	$author = DatabaseObject_Comment::getUserNameByCommentId($this->db, $options);

	    	// get the text array
	    	$text = DatabaseObject_Comment::getCommentTextByCommentId($this->db, $options);
        	
	    	// send to template
	    	$templater->quote = "[quote={$author}]".$comment."[/quote]";
	        
	    	// Get current user_id if any
	        $auth = Zend_Auth::getInstance();
	    	if ($auth->hasIdentity()) {
	    		// User has dirty words filtered?
	    		if($this->filterDirty = DatabaseObject_User::checkDirtyFilter($this->db, $auth->getIdentity()->user_id)) {
					$templater->excerpt = UtilityController::cleanDirtyWords($templater->excerpt);
	    		}
	    	}
	
	        // Send text through the Smarty template
	        $output = $templater->render('comment/' . $tpl);
	
	        // Output it to screen
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

	public function updateresourceusernameAction()
	{
		if($this->identity && $this->_request->isXmlHttpRequest()) {
	        
			$replyToCommentId = (int) $this->_request->getParam('comment_id');
	
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
	        
			// select options
	        $options = array('comment_id' => (int) $replyToCommentId
	        			);
	        
	        $output = DatabaseObject_Comment::getUserNameByCommentId($this->db, $options);
			
	        echo $output;
		} else {
			die("Access Denied");
		}
	}

    /**
     * This is the Ravelry style quote box ajax method
     *
     */
	public function showcommentreplyAction()
    {
        $templater = new Templater();
		$tpl = 'comment-response_to.tpl';

		$this->comment_id = $this->_request->getParam('reply_to_id');
		$this->depth = $this->_request->getParam('depth');
		$pageNumber = $this->_request->getParam('page');

		// Turns off automatic rendering to the template
		$this->_helper->viewRenderer->setNoRender();

		// Get the comment row
       	$comment = DatabaseObject_Comment::getCommentById($this->db, array('comment_id' => $this->comment_id));

       	if ($this->depth) {
	       	$depth = $this->depth;
	       	$depth++;
       	} else {
       		$depth = 1;
       	}
		
       	// Is this also a reply to a specific comment? Let's get the author and reply #
	    if ($comment['reply_to_id'] > 0)
		{
			// Do my DB calls...
			$replyComment = DatabaseObject_Comment::getCommentById($this->db, array('comment_id' => $comment['reply_to_id']));
			$replyAuthorName = DatabaseObject_User::getUserNameById($this->db,array('user_id' => $replyComment['user_id']));
			$replyCommentNumber = $replyComment['comment_num'];

			$templater->replyAuthorName = $replyAuthorName;
	        $templater->replyCommentNumber = $replyCommentNumber;
		}

        $templater->commentText = htmlspecialchars_decode($comment['comment']);
    	
        
        //TODO: Move dirty word filter into ONE place.
        
        // Get current user_id if any
        //$auth = Zend_Auth::getInstance();
    	if (isset($this->identity)) {
    		// User has dirty words filtered?
    		if(!DatabaseObject_User::checkDirtyFilter($this->db, $this->identity->user_id)) {
				$templater->commentText = UtilityController::cleanDirtyWords($templater->commentText);
    		}
    	} else {
			// No user. Let's filter those bad words
    		$templater->commentText = UtilityController::cleanDirtyWords($templater->commentText);
    	}

        // Template vars
		$templater->replyToId = $comment['reply_to_id'];
		$templater->rsrcId = $comment['rsrc_id'];
        $templater->depth = $depth;
        $templater->pageNumber = $pageNumber;
        $templater->commentId = $comment['comment_id'];
        $templater->commentUserName = $comment['user_name'];
        $templater->commentNum = $comment['comment_num'];
        $templater->rsrcUrl = DatabaseObject_Resource::getResourceUrl($this->db,$comment['rsrc_id']);
	        
        // Send text through the Smarty template
        $output = $templater->render('comment/' . $tpl);
	
        // Output it to screen
        echo $output;
    }

	public function savelastcommentAction()
	{
		if($this->_request->isXmlHttpRequest()) {
		
			$userId = (int) $this->_request->getParam('id');
			$text = $this->_request->getParam('text');
			
    		// Turns off automatic rendering to the template
    		$this->_helper->viewRenderer->setNoRender();

    		$memcache = new Memcache;
		    $memcache->connect("localhost",11211);
			
	        $memcache->set("{$userId}:lastUserComment", $text, false, 3600);
		}
	}
    
}
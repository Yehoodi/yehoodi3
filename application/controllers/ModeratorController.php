<?php

/**
 * Yehoodi 3.0 ModeratorController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
class ModeratorController extends CustomControllerAction 
{

	public function init()
	{
        parent::init();
	} // init

	
	/**
	 * For viewing comment history
	 *
	 */
	public function commenthistoryAction()
	{
        // get any post/get info
    	$request = $this->getRequest();
    	$comment_id = $request->commentId;

    	$options = array('comment_id'  =>  $comment_id,
    	                 'order'       =>  'ch.date_edited ASC'
    	                 );
	    
	    $history = DatabaseObject_CommentHistory::getRevisionHistory($this->db, $options);
	    $diffResult = array();
        $lastEntry = '';
        	    
	    //Zend_Debug::dump($history);die;
	    foreach ( $history as $entry ) {
	        $diffResult[] = array(
	                             'date_edited'     => $entry['date_edited'],
	                             'title'           => $entry['title'],
	                             'comment'         => common::htmlDiff($lastEntry, $entry['comment']),
	                             'user_name'       => $entry['user_name']
	                             );

            $lastEntry = $entry['comment'];
	    }
	    
	    $currentEntry = DatabaseObject_Comment::getCommentById($this->db, array('comment_id' => $comment_id));
	    
	    $diffResult[] = array(
	                             'date_edited'     => $currentEntry['date_edited'],
	                             'title'           => $currentEntry['title'],
	                             'comment'         => common::htmlDiff($lastEntry, $currentEntry['comment']),
	                             'user_name'       => $currentEntry['user_name']
	                             );
	    
	    $this->view->history = $diffResult;
	    
    	// Render!
        $this->_helper->viewRenderer('comment-history');
	}

	/**
	 * For viewing IP address history
	 *
	 */
	public function iphistoryAction()
	{
        // get any post/get info
    	$request = $this->getRequest();
    	$commentId = $request->commentId;

    	$comment = new DatabaseObject_Comment($this->db);
    	$comment->load($commentId);
    	
    	$remoteIP = long2ip($comment->remote_ip);
    	if ($remoteIP != '127.0.0.1') {
        	
        	// Get # of posts for this IP address
        	$this->view->postNum = DatabaseObject_Comment::getPostIPCount($this->db, array('remote_ip' => $comment->remote_ip));
        	
        	// Get all user_names for this IP address
        	$this->view->userNames = DatabaseObject_Comment::getUserNameForIP($this->db, array('remote_ip' => $comment->remote_ip));
        	
        	// Get the IP addresses this user posted under
        	$results = DatabaseObject_Comment::getIPForUserName($this->db, array('user_id' => $comment->user_id));
        	
        	$postedIPs = array();
        	foreach ($results as $value){
        	    $postedIPs[] = long2ip($value['remote_ip']);
        	}
        	$this->view->postedIPs = $postedIPs;
        	
        	$this->view->ip = $remoteIP;
        	$this->view->comment = $comment;
    	}
        	
        	// Render!
            $this->_helper->viewRenderer('ip-history');
	}

}
<?php
    class DatabaseObject_CommentMeta
    {
        public $postedBy = null;
        public $relativeDate = null;
        public $resourceUrl= null;
        public $categoryName = null;
        public $resourceName = null;
        public $commentPageNum = null;
        public $resourceTitle = null;
        public $signature = null;
        protected $rsrc_id = null;
        protected $commentCount = null;
        
        
        
    	/**
    	 * Takes a comment object and uses protected
    	 * methods to set this objects
    	 * properties for all the meta information
    	 * for the comment
    	 *
    	 * @param object $db
    	 * @param int $comment_id
    	 */
        public function __construct($db, $comment = null)
        {
            if (is_object($comment)) {
	        	$this->_db = $db;
	        	
	        	//Zend_Debug::dump($comment->getId());die;
	        	
                $this->_id = $comment->getId();
	            $this->user_id = $comment->user_id;
	            $this->rsrc_id = $comment->rsrc_id;
	            $this->comment_num = $comment->comment_num;
	            $this->commentCount = DatabaseObject_Comment::getCommentCount($this->_db, array('rsrc_id' => $this->rsrc_id));

	            // Set other properties
	            $this->setPostedBy();
	        	$this->setRelativeDate();
	        	$this->setResourceUrl();
	        	$this->setCategoryAndNameId();
	        	$this->setResourceAndCategoryName();
            }
        }

        /**
         * Who posted this comment?
         *
         * @param object $db
         * @param int $id
         */
        public function setPostedBy()
        {
            // instantiate a Zend select object
            $select = $this->_db->select();
            
            // get the username
            $select->from('user','user_name')
            	   ->where('user_id = ?', $this->user_id);
            
            $this->postedBy = $this->_db->fetchOne($select);
        }//setPostedBy
                
        /**
         * Sets the relative Date for the resource
         * Example: (8 days ago)
         *
         * @param object $db
         * @param int $id
         */
        public function setRelativeDate()
        {
            // instantiate a Zend select object
            $select = $this->_db->select();
            
            // First get the category id
            $select->from('comment','date_created')
            	   ->where('comment_id = ?', $this->_id);
            
            //Zend_Debug::dump($select->__toString());die;
            $result = $this->_db->fetchOne($select);
            
            $this->relativeDate = common::getRelativeTime($result);
        }//setRelativeDate

        /**
         * Sets the properties for resource page url
         * and the page number for the comment
         *
         * @param object $db
         * @param int $id
         */
        protected function setResourceUrl()
        {
            // get the number of comments per page from the config
            $commentsPerPage = Zend_Registry::get('paginationConfig')->CommentsPerPage;
        	
            // calculate the page number this comment is on
			if($this->comment_num < $commentsPerPage) {
				$this->commentPageNum = 1;
			} else {
				$this->commentPageNum = ceil($this->comment_num / $commentsPerPage);
			}
            
			//var_dump($this->commentPageNum);die;
            // get the url from the static method in DatabaseObject_Resource
            $urlSEO = DatabaseObject_Resource::getResourceUrl($this->_db, $this->rsrc_id);
            
            $rootUrl = "{$this->rsrc_id}/{$urlSEO}/";

            $this->resourceUrl = $rootUrl;
        }

        /**
         * Sets the properties for category id
         * and resource id from a given Resource
         *
         * @param object $db
         * @param int $id
         */
        protected function setCategoryAndNameId()
        {
            // instantiate a Zend select object
            $select = $this->_db->select();
            
            // First get the category id
            $select->from('resource',array('cat_id','title'))
            	   ->where('rsrc_id = ?', $this->rsrc_id);
            
            //Zend_Debug::dump($select->__toString());die;
            $result = $this->_db->fetchAll($select);

            $this->cat_id = $result[0]['cat_id'];
            $this->resourceTitle = $result[0]['title'];
        }
        
        /**
         * Sets the properties for Category name
         * and resource name
         *
         * @param object $db
         * @param int $cat_id
         */
        protected function setResourceAndCategoryName()
        {
            // instantiate a Zend select object
            $select = $this->_db->select();
            
            // now get the category Name
            $select->from('category',
            				array('cat_type',
            					  'cat_id'
            				)
            			  )
            	   ->join('resource_type',
            	   		  'resource_type.rsrc_type_id = category.rsrc_type_id',
            	   		   array('rsrc_type',
            	   		   		 'rsrc_type_id')
            	   		   )
            	   ->where('cat_id = ?',
            	   		   $this->cat_id)
            	   		 ;
            
        	$result = $this->_db->fetchAll($select);
        	
        	$this->categoryName = $result[0]['cat_type'];
        	$this->resourceName = $result[0]['rsrc_type'];
        }//setResourceAndCategoryName

    }
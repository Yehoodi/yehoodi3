<?php
    
/**
 * Yehoodi 3.0 CustomControllerAction Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * All Yehoodi classes extend from this for 
 * required functionality
 *
 */
class CustomControllerAction extends Zend_Controller_Action
    {
        public $db; // database
        public $breadcrumbs; // breadcrumb trail
        //public $messenger;

        public function init()
        {
         	// Closed? Kill the postDispatch() section also. (Possibly make this a config setting.)
			if (Zend_Registry::get('serverConfig')->site == "off") {
             	$this->_redirect('/closed');die;
			}
				
           // Database connection
        	$this->db = Zend_Registry::get('db');
        	
        	// Breadcrumb trail
        	$this->breadcrumbs = new Breadcrumbs();
            $this->view->pageCurrent = 1;
        	//$this->breadcrumbs->addStep('Home', $this->getUrl(null, 'index'));

        	// Banned IP check
        	//$bannedIPs = DatabaseObject_SiteBan::getBannedIPArray($this->db);
        	
//        	if(in_array($_SERVER['SERVER_ADDR'], $bannedIPs)) {
//        		$this->_redirect('/closed');
//        	}
        }

        /**
         * Eliminates hard coded URLs in Controller Actions
         *
         * @param string $action
         * @param string $controller
         * @return string
         */
        public function getUrl($action = null, $controller = null)
        {
        	//$url = '';
        	//$url  = rtrim($this->getRequest()->getBaseUrl(), '/') . '/';
        	$url  = rtrim($this->getRequest()->getBaseUrl(), '/');
        	$url .= $this->_helper->url->simple($action, $controller);
        	
        	return '/' . ltrim($url, '/');
        }
        
        /**
         * Called just before a controller action is started
         *
         */
        public function preDispatch()
        {
        	// Get the extension for javascript files from the config
        	$this->view->jsExt = Zend_Registry::get('serverConfig')->jsExt;
        	$this->view->env = Zend_Registry::get('serverConfig')->env;
        	
        	$auth = Zend_Auth::getInstance();
        	
        	if (!$auth->hasIdentity()) {
	        	
        		// This is all for the remember me cookie check
	        	if(isset($_COOKIE['y3li'])) {
	                parse_str($_COOKIE['y3li']);
	                
	                $username = $usr;
	                $password = $hash;
	                
					// unscramble the md5 from the cookie
					$password = str_split($password, 8);
					$passUnscram = $password[3].$password[1].$password[2].$password[0];
					$passUnscram = strrev($passUnscram);
					
					// setup the authentication adapter
	                $adapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'),
	                                                         'user',
	                                                         'user_name',
	                                                         'password',
	                                                         '?');
	
	                $adapter->setIdentity($username);
	                $adapter->setCredential($passUnscram);
	
	                // try and authenticate the user
	                $result = $auth->authenticate($adapter);
	
	                if ($result->isValid()) {
	                    $user = new DatabaseObject_User(Zend_Registry::get('db'));
	                    $user->load($adapter->getResultRowObject()->user_id);
	
				        // record login attempt
	                    $user->loginSuccess();
	
	                    // create identity data and write it to session
	                    $identity = $user->createAuthIdentity();
	                    $auth->getStorage()->write($identity);

	                    // Cookie log in complete.
	                    // Now reload and redirect to where the user wanted to go
						$this->_redirect($this->getRequest()->getServer('REQUEST_URI'));
	                }        		
	        	}

	        	// no user or valid cookie found
	        	$this->view->authenticated = FALSE;

        	} else {
        		$this->view->authenticated = TRUE;
        		$this->view->identity = $auth->getIdentity();
        		
        		$this->view->unreadMailCount = DatabaseObject_MailStatus::getNewMailCount(Zend_Registry::get('db'), array('user_id' => $auth->getIdentity()->user_id));

        		// get the avatar for the user
                $this->avatar = new DatabaseObject_UserAvatar(Zend_Registry::get('db'));
                $avatar_id = DatabaseObject_UserAvatar::loadAvatarId(Zend_Registry::get('db'), $auth->getIdentity()->user_id);
                $this->avatar->load($avatar_id);
                
                $this->view->myAvatar = $this->avatar;
        	}
        	
        	// Memcache skipping
        	if (Zend_Registry::get('serverConfig')->allow_cache_clear && (!empty($_REQUEST['clear_cache']))) {
                DatabaseObject_Resource::clearResourceCache();
            }
            
            // Version for reloading css & javascript
            $this->view->version = Zend_Registry::get('serverConfig')->version;

        }
        
        /**
         * Called just after a controller action is completed
         *
         */
        public function postDispatch()
        {
            // Check if "global" vars are set (to kill php notice)
        	if (!isset($this->view->q) ) {
        		$this->view->q = '';
            }
        	
            if (!isset($this->view->messages) ) {
        		$this->view->messages = array();
            }
        	
			if (Zend_Registry::get('serverConfig')->site == "on") {
            	// kill this section if the site is closed
            	if($this->breadcrumbs) {
    	        	$this->view->breadcrumbs = $this->breadcrumbs;
    	        	$this->view->title = $this->breadcrumbs->getTitle();
            	}
            	$this->view->controller = $this->_request->getParam('controller');

            	// Google Map config
            	$this->view->mapConfig = Zend_Registry::get('mapConfig');
            	
            	// Facebook API Key
            	//$this->view->fbKey = Zend_Registry::get('facebookConfig');
            	
            	// Update the user's last updated time
    			$auth = Zend_Auth::getInstance();
    
            	if ($auth->hasIdentity()) {
            	    $currentTimeDate = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
    				$this->db->query("UPDATE user SET last_updated_time = '{$currentTimeDate}' WHERE user_id = ?", $auth->getIdentity()->user_id);
            	}
            	
            	// What is the URL of the site we are working on?
            	$this->view->siteURL = Zend_Registry::get('serverConfig')->location;
    		}

        	// Throw april fools joke
//			if ( !isset($_COOKIE['y3gag2011']) ) {
//				$this->aprilFools();
//			}

                // Debugging
                //$memcache = new Memcache;
    		    //$memcache->connect("localhost",11211);
    		      //var_dump($memcache->get("1762:lastUserComment"));

        }

        /**
         * Generates a token for submit pages to
         * avoid form forgeries
         *
         * @param string $seed
         * @return string
         */
        protected function generateToken($seed='jessicaAlb@')
		{
			$token = md5($seed.mktime());
			$globalSession = new Zend_Session_Namespace('global_data');
			$globalSession->token = $token;
			return $token;
		}
		
		protected function tokenCheck($tokenToCheck = '')
		{
			$globalSession = new Zend_Session_Namespace('global_data');
			$returnValue =(!empty($tokenToCheck) and $tokenToCheck == $globalSession->token);
			return $returnValue;
		}
		
		/**
		 * returns Json data for controller actions
		 *
		 * @param array $data
		 */
		public function sendJson($data)
		{
			// Turns off automatic rendering to the template
			$this->_helper->viewRenderer->setNoRender();
			
			// Send the proper json http header
			$this->getResponse()->setHeader('content-type', 'application/json');
			echo Zend_Json::encode($data);
		}

	/**
	 * Builds the pagination for the
	 * Comment page
	 * 
	 * uses the smarty template in
	 * modules/modules.pagination.tpl
	 *
	 * @param int $page
	 * @param int $totalitems
	 * @param int $limit
	 * @param int $adjacents
	 * @param string $targetpage
	 * @param string $pagestring
	 */
	public function getPaginationString($page = 1, $totalitems, $limit = 20, $adjacents = 1, $targetpage = "/", $pagestring = "/", $urlQueryString = '')
	{		
		// Vars
		$prev = $page - 1;									//previous page is page - 1
		$next = $page + 1;									//next page is page + 1
		$lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
        $page = max(1, min($lastpage, $page));
		
		$lpm1 = $lastpage - 1;								//last page minus 1
		$pageCounterTop = array();							// arrays to hold the page counts
		$pageCounterMidle = array();
		$pageCounterEnd = array();
		
		$this->view->pageCurrent = $page;
		
		if($urlQueryString) {
			$urlQueryString = "?view=" . $urlQueryString;
		}
		
		/* 
			Now we apply our rules and draw the pagination object. 
			We're actually saving the code to a variable in case we want to draw it more than once.
		*/
		if($lastpage > 1)
		{	
			//previous button
			if ($page > 1) 
				$this->view->pagePrevious = $targetpage . $pagestring . $prev . $urlQueryString;
			else
				$this->view->pagePrevious = "";
			
			//pages	
			if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page){
						$this->view->pageCurrent = $counter;
						$pageCounterTop[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
					}else{
						$pageCounterTop[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
					}
				}
			}
			elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
			{
				//close to beginning; only hide later pages
				if($page < 1 + ($adjacents * 3))		
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page){
							$this->view->pageCurrent = $counter;
							$pageCounterTop[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
						}else{
							$pageCounterTop[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
						}
					}
					$pageCounterEnd[$lpm1] = $targetpage . $pagestring . $lpm1. $urlQueryString;
					$pageCounterEnd[$lastpage] = $targetpage . $pagestring . $lastpage. $urlQueryString;
					$this->view->pageLast = $targetpage . $pagestring . $lastpage;
				}
				//in middle; hide some front and some back
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pageCounterTop[1] = $targetpage . $pagestring . "1". $urlQueryString;
					$pageCounterTop[2] = $targetpage . $pagestring . "2". $urlQueryString;
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page){
							$this->view->pageCurrent = $counter;
							$pageCounterMidle[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
						}else{
							$pageCounterMidle[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
						}				
					}
	
					$pageCounterEnd[$lpm1] = $targetpage . $pagestring . $lpm1. $urlQueryString;
					$pageCounterEnd[$lastpage] = $targetpage . $pagestring . $lastpage. $urlQueryString;
	
					$this->view->pageLast = $targetpage . $pagestring . $lastpage;
				}
				//close to end; only hide early pages
				else
				{
					//$pageCounterTop[1] = "/".$pagestring."1";
					$pageCounterTop[1] = $targetpage . $pagestring . "1". $urlQueryString;
					//$pageCounterTop[2] = "/".$pagestring."2";
					$pageCounterTop[2] = $targetpage . $pagestring . "2". $urlQueryString;
					for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page){
							$this->view->pageCurrent = $counter;
							$pageCounterEnd[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
						}else{
							$pageCounterEnd[$counter] = $targetpage . $pagestring . $counter. $urlQueryString;
						}
					}
				}
			}
			
			//next button
			if ($page < $counter - 1) 
				$this->view->pageNext = $targetpage . $pagestring . $next . $urlQueryString;
		}
		
		$this->view->pageCounterTop = $pageCounterTop;
		$this->view->pageCounterMiddle = $pageCounterMidle;
		$this->view->pageCounterEnd = $pageCounterEnd;
		$this->view->pageLast = $lastpage;
		
	}//getPaginationString

	/**
	 * Strips and cleans search queries before
	 * getting to Solr
	 *
	 * @param string $text
	 * @return string
	 */
	public function htmlspecialchars_decode( $text ) {
			
			$text = str_replace("&amp;", "&", $text );
			$text = str_replace("&quot;", "\"", $text );
			$text = str_replace("&#039;", "'", $text );
			$text = str_replace("&lt;", "<", $text );
			$text = str_replace("&gt;", ">", $text );
			
			return $text;
		}

    
	/**
	 * Catch-all Action for invalid url requests
	 *
	 * @param unknown_type $action
	 * @param unknown_type $arguments
	 */
	function __call($action, $arguments)
    {
		// Invalid controller specified
		// Redirect to the top
		
		$this->_redirect('/discussion/all');
		$this->_redirect($this->getUrl('all', 'discussion'));
		
    }
    
    /**
	 * Logs a cache hit
	 *
	 */
	public function logCacheHit()
	{
            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
				$message = sprintf('CACHE HIT AT: %s: %s',
		                           $_SERVER['REMOTE_ADDR'],
		                           date("Y-m-d H:m:s"));
		
		        $logger = Zend_Registry::get('cachelogger');
		        $logger->notice($message);
            }
	}

    public function aprilFools() {
    	$cookieName = 'y3gag2011';
    	$cookie_time = (3600 * 24 * 3); // 3 days
		setcookie($cookieName, 1, time() + $cookie_time, '/', 'yehoodi.com');
		
		header('Location: /swingdancepros');
    }

    public static function is_smart_device()
    {
        $smart_devices = array('iPad', 'iPhone');
        $smart_device = array();
        foreach ($smart_devices as $device) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], $device)) {
                $smart_device['name'] = $device;
                return $smart_device['name'];
            }
        }
        return false;
    }
}
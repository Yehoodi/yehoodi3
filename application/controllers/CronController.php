<?php

/**
 * Yehoodi 3.0 CronController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Creates various RSS feeds for Yehoodi
 * 
 * This controller is to be called from a cron job.
 * 
 * It works by calling the full URL:
 * http://yehoodi.com/cron/{resource}?key={cronKey}
 * 
 * The {resource} is either a resource or a podcast {heymisterjesse|yehooditalk|yehoodivideo|sausagebeaver}
 * 
 * The rss feed is created in the /public/rss/ dir.
 *
 */
class CronController extends CustomControllerAction 
{
	
	const SHOW_PATH = '/var/www/sites/yehoodi3.com/shows/';
	
	protected $action;
	protected $resource;
	
	public function init()
	{
		parent::init();
		$request = $this->_request;
		
        // Get the page params
    	$this->action = $request->getParam('action');
    	$this->resource = $request->getParam('resource');
    	$this->key = $request->getParam('key');
    	
    	if($this->key != Zend_Registry::get('serverConfig')->cronKey) {
    	    die('Invalid request');
    	}
    	
	} // init

	public function indexAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
        
		// array to hold the RSS feed entries
		$entries = array();
		
		// Get the appropriate topics
		$topics = new DatabaseObject_Resource($this->db);

    	// Set up new date time
        $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
		$this->today = $dateTime->format("Y-m-d H:i:s");

		/**
		 * Build the options based on the
		 * resource given in the URL
		 */
		switch ($this->resource) {
    	    case 'heymisterjesse':
        		// Header values
        		$this->title = "Hey Mister Jesse";
        	    $this->link = "http://www.yehoodi.com/show/{$this->resource}";
                $this->editor = "Jesse Miner";
                $this->email = "jesse@yehoodi.com";
                $this->description = "Welcome to Hey Mister Jesse's barbecue of tasty talk about swingin' jazz and blues. DJ Jesse Miner will serve up a platter of swingin' music that matters to dancers.";
                $this->keywords = 'swing,jazz,dance,lindy';
                $this->rating = 'clean';
                $this->includeMedia = true;
                $this->includeITunes = true;
                $this->includeGuid = true;
                
                // Image stuff
    	        $this->imageURL = "http://www.yehoodi.com/images/graphics/HMJ_Logo_150.png";
    	        $this->imageTitle = $this->title;
    	        $this->imageLink = $this->link;
    	        $this->imageDescription = $this->description;
    	        $this->imageWidth = 144;
    	        $this->imageHeight = 141;
    	        
                $options = array('show_code' => ShowController::SHOW_HEY_MISTER_JESSE,
                                 );
                $hmjResources = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);
                
                $options = array('rsrc_id' => $hmjResources,
        						 'order'		=> 'r.rsrc_date DESC'
                                );
                break;
    	        
    	    case 'yehoodivideo':
        		// Header values
        		$this->title = "The Yehoodi Talk Show: Video Edition";
        	    $this->link = "http://www.yehoodi.com/show/{$this->resource}";
                $this->editor = "Manu Smith";
                $this->email = "spuds@yehoodi.com";
                $this->description = "The Yehoodi Video Talk Show brings the latest swing related videos and general silliness to your desktop. Join the Yehoodi crew as they watch just about anything that does and doesn't swing.";
                $this->keywords = 'swing,jazz,dance,lindy';
                $this->rating = 'yes';
                $this->includeMedia = true;
                $this->includeITunes = true;
                $this->includeGuid = true;
                
                // Image stuff
    	        $this->imageURL = "http://www.yehoodi.com/images/graphics/YtK_iTunes.png";
    	        $this->imageTitle = $this->title;
    	        $this->imageLink = $this->link;
    	        $this->imageDescription = $this->description;
    	        $this->imageWidth = 144;
    	        $this->imageHeight = 141;
    	        
                $options = array('show_code' => ShowController::SHOW_YEHOODI_VIDEO ,
                                 );
                $hmjResources = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);
                
                $options = array('rsrc_id' => $hmjResources,
        						 'order'		=> 'r.rsrc_date DESC'
                                );
                break;

            case 'swingnation':
                // Header values
                $this->title = "SwingNation";
                $this->link = "http://www.yehoodi.com/show/{$this->resource}";
                $this->editor = "Manu Smith";
                $this->email = "spuds@yehoodi.com";
                $this->description = "SwingNation is the latest creation from Yehoodi. join Spuds, ZuckerPunch and Rikomatic as they bring you news, interviews and videos from the Lindy Hop world.";
                $this->keywords = 'swing,jazz,dance,lindy';
                $this->rating = 'yes';
                $this->includeMedia = true;
                $this->includeITunes = true;
                $this->includeGuid = true;

                // Image stuff
                $this->imageURL = "http://www.yehoodi.com/images/graphics/SwingNationShowPageImg.png";
                $this->imageTitle = $this->title;
                $this->imageLink = $this->link;
                $this->imageDescription = $this->description;
                $this->imageWidth = 144;
                $this->imageHeight = 141;

                $options = array('show_code' => ShowController::SHOW_SWINGNATION ,
                );
                $hmjResources = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

                $options = array('rsrc_id' => $hmjResources,
                    'order'		=> 'r.rsrc_date DESC'
                );
                break;

            case 'yehooditalkshow':
        		// Header values
        		$this->title = "The Yehoodi Talk Show: Audio Edition";
        	    //$this->link = "http://www.yehoodi.com/show/{$this->resource}";
        	    $this->link = "http://www.yehoodi.com/show/{$this->resource}";
                $this->editor = "Manu Smith";
                $this->email = "spuds@yehoodi.com";
                $this->description = "First launched in 1998 and now re-re-launched in 2005, the Yehoodi Talk Show brings the latest swing and lindy hop news, interviews and general silliness to your desktop. Join the Yehoodi crew as they chat about anything that does and doesn't swing.";
                $this->keywords = 'swing,jazz,dance,lindy';
                $this->rating = 'yes';
                $this->includeMedia = true;
                $this->includeITunes = true;
                $this->includeGuid = true;
                
                // Image stuff
    	        $this->imageURL = "http://www.yehoodi.com/images/graphics/YtK_iTunes.png";
    	        $this->imageTitle = $this->title;
    	        $this->imageLink = $this->link;
    	        $this->imageDescription = $this->description;
    	        $this->imageWidth = 144;
    	        $this->imageHeight = 141;
    	        
                $options = array('show_code' => ShowController::SHOW_YEHOODI_TALK,
                                 );
                $hmjResources = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);
                
                $options = array('rsrc_id' => $hmjResources,
        						 'order'		=> 'r.rsrc_date DESC'
                                );
                break;
    	        
    	    case 'sausagebeaver':
        		// Header values
        		$this->title = "Sausage Fest / Beaver Lodge";
        	    $this->link = "http://www.yehoodi.com/show/{$this->resource}";
                $this->editor = "Manu Smith";
                $this->email = "spuds@yehoodi.com";
                $this->description = "What do Lindy Hop guys talk about when the ladies aren't around? What do Lindy Hop ladies talk about when the fellas aren't around. Tune into Sausave Fest (Hosted by Spuds) and Beaver Lodge (Hosted by Mouth) to find out!";
                $this->keywords = 'swing,jazz,dance,lindy';
                $this->rating = 'yes';
                $this->includeMedia = true;
                $this->includeITunes = true;
                $this->includeGuid = true;
                
                // Image stuff
    	        $this->imageURL = "http://www.yehoodi.com/images/graphics/SF-BL_small.png";
    	        $this->imageTitle = $this->title;
    	        $this->imageLink = $this->link;
    	        $this->imageDescription = $this->description;
    	        $this->imageWidth = 144;
    	        $this->imageHeight = 141;
    	        
                $options = array('show_code' => ShowController::SHOW_SAUSAGEBEAVER,
                                 );
                $resources = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);
                
                $options = array('rsrc_id' => $resources,
        						 'order'		=> 'r.rsrc_date DESC'
                                );
                break;

            case 'swingnation_audio':
                // Header values
                $this->title = "SwingNation (Audio Only)";
                //$this->link = "http://www.yehoodi.com/show/{$this->resource}";
                $this->link = "http://www.yehoodi.com/show/{$this->resource}";
                $this->editor = "Manu Smith";
                $this->email = "spuds@yehoodi.com";
                $this->description = "SwingNation is the latest creation from Yehoodi. Join Spuds, ZuckerPunch and Rikomatic as they bring you news, interviews and videos from the Lindy Hop world.";
                $this->keywords = 'swing,jazz,dance,lindy';
                $this->rating = 'yes';
                $this->includeMedia = true;
                $this->includeITunes = true;
                $this->includeGuid = true;

                // Image stuff
                $this->imageURL = "http://www.yehoodi.com/images/SwingNationShowPageImg.png";
                $this->imageTitle = $this->title;
                $this->imageLink = $this->link;
                $this->imageDescription = $this->description;
                $this->imageWidth = 180;
                $this->imageHeight = 210;

                $options = array('show_code' => ShowController::SHOW_SWINGNATION_AUDIO,
                );
                $resources = DatabaseObject_Resource::getResourcesByShowCode($this->db, $options);

                $options = array('rsrc_id' => $resources,
                    'order'		=> 'r.rsrc_date DESC'
                );
                break;

    	    case 'featured':
    	    case 'lindy':
    	    case 'event':
    	    case 'lounge':
    	    case 'biz':
        		
        		// Header values
                $this->title = "Yehoodi.com: {$this->resource}";
        	    $this->link = "http://www.yehoodi.com/discussion/{$this->resource}";
                $this->editor = "Yehoodi.com";
                $this->email = "webmaster@yehoodi.com";
                $this->description = "RSS feed for topics from the Yehoodi.com Lindy Hop website";
                $this->includeMedia = false;
                $this->includeITunes = false;
                $this->includeGuid = false;


                // Get month for the latest activity sort order
        		$dateTime->modify('-1 month');
        		$lastMonth = $dateTime->format("Y-m-d H:i:s");
        
        		// Which resource type are we getting?
        		if(!$rsrcType = DatabaseObject_ResourceType::getResourceTypeIdByUrl($this->db, $this->resource)) {
        			return;
        		}
        		
        		// Build the options
        		$options = array('rsrc_type_id' => $rsrcType,
        						 'limit'		=> 25,
        						 'order'		=> 'r.rsrc_date DESC',
        						 'rsrc_date'	=> $lastMonth,
        						 'now_date'		=> $this->today);

        		break;
    	        
    	    default:
    	        
    	        break;
    	}
    	

		// Options are set...
    	// get the resources
		//Zend_Debug::dump($options);die;
		$results = $topics->getResourceById($this->db, $options);
		//Zend_Debug::dump($results);die;
		
		// build the data into arrays
		foreach($results as $result ) {
			$title = preg_replace('/[^\x20-\x7E]/', ' ', strip_tags(html_entity_decode($result->extended->show_name)));
			//$link = $result->url;
			
			// 1. Decode any encoded html entities
			$description = html_entity_decode($result->descrip);
			
        	// 3. Strip all html
        	$description = strip_tags($description);
        	
        	// 4. Strip anything that isn't ASCII
			$description = preg_replace('/[^\x20-\x7E]/', ' ', $description);
			
			// 5. Truncate it
			$description = $this->_truncate($description, 500);
			
			// 6. Get the filesize of the media for valid rss
//			if (!$filesize = filesize(self::SHOW_PATH . $result->extended->media_url)) {
//				$filesize = 1;
//			}

			$remoteFile = $result->extended->media_url;
			$filesize = 1;
			$ch = curl_init($remoteFile);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //not
			
			$data = curl_exec($ch); 
			curl_close($ch); 
			if ($data === false) { 
				echo 'cURL failed'; 
				exit; 
			}
			
			$contentLength = 'unknown';
			$status = 'unknown';
			if (preg_match('/^HTTP\/1\.[01] (\d\d\d)/', $data, $matches)) {
			  $status = (int)$matches[1];
			}
			if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
			  $contentLength = (int)$matches[1];
			}
			
			//echo 'HTTP Status: ' . $status . "\n";
			//echo 'Content-Length: ' . $contentLength;
			$filesize = $contentLength;
			//var_dump($contentLength);die;
			
			// Build array
			$entry = array(
							'title'			=> $title,
							//'link'			=> 'http://yehoodi.com/comment/'. $result->getId() . '/' . $result->resourceSeoUrlString,
							'link'			=> 'http://www.yehoodi.com/show/'. $this->resource . '?episode=' . $result->extended->show_episode,
							'description'	=> $description,
							'dc'	    	=> array('creator' => htmlentities($result->extended->artist)),
							'lastUpdate'    => strtotime($result->meta->rsrc_date),
							'published'     => $result->meta->rsrc_date,
							'comments'      => 'http://www.yehoodi.com/comment/'. $result->getId() . '/' . $result->resourceSeoUrlString
							);
							
			if($this->includeMedia) {
			     $entry['enclosure'] = array(
					                     array('url'   => $result->extended->media_url,
					                           'type'  => "audio/mpeg",
					                     	   'length' => $filesize)
					                         );
			}

			if($this->includeGuid) {
			     $entry['guid'] = $this->link . '?episode=' . $result->extended->show_episode;
			}
			
			array_push($entries, $entry);
		}
		
		$this->processFeed($entries);
		

    }
    
    public function processFeed($entries)
    {
		// Vars for the Header section of all the RSS feeds
		$title            = $this->title;
		$description      = $this->description;
		$copyright        = "2013 Yehoodi.com";
		$language         = "en-us";
		$link             = $this->link;
		$lastUpdate       = strtotime($this->today);
		$author           = $this->editor;
		$email			  = $this->email;
		$pubDate          = strtotime($this->today);
        $webMaster        = "webmaster@yehoodi.com";
        $generator        = "Yehoodi RSS Generator (1.0) http://www.yehoodi.com";
        
        // Image tag
        $imageURL           = $this->imageURL;
        $imageTitle         = $this->imageTitle;
        $imageLink          = $this->imageLink;
        $imageDescription   = $this->imageDescription;
        $imageWidth         = $this->imageWidth;
        $imageHeight        = $this->imageHeight;
		
        // iTunes specific Stuff
        $itunes = array(
            // optional, default to the main author value
            'author' => $author,
    
            // optional, default to the main author value
            // Owner of the podcast
            'owner' => array(
                'name'  => 'Yehoodi.com',
                'email' => 'info@yehoodi.com'
            ),
    
            // optional, default to the main image value
            'image' => $imageURL,
    
            // optional, default to the main description value
            //'subtitle' => 'short description',
            'summary'  => $this->description,
    
            // optional
            //'block' => 'Prevent an episode from appearing (yes|no)',
    
            // required, Category column and in iTunes Music Store Browse
            'category' => array(
                // up to 3 rows
                array(
                    // required
                    'main' => 'Arts',
    
                    // optional
                    //'sub'  => 'sub category'
                )
            ),
    
            // optional
            //'explicit'     => 'parental advisory graphic (yes|no|clean)',
            'explicit'     => $this->rating,
            'keywords'     => $this->keywords,
            //'new-feed-url' => 'used to inform iTunes of new feed URL location'
        );

        $rss = array(
        		'author'		=> $author,
        		'email'			=> $email,
				'dc'   			=> array('creator' => $author),
        		'title'			=> $title,
				'description'   => $description,
				'generator'     => $generator,
				'link'			=> $link,
				'copyright'		=> $copyright,
				'charset' 		=> 'UTF-8',
				'lastUpdate'    => $lastUpdate,
				'published'     => $pubDate,
				'language' 		=> $language,
				'image'         => $imageURL,
				'webmaster'     => $webMaster,
				'entries'		=> $entries,
        		'atom'			=> 'atom="http://www.w3.org/2005/Atom'
				);
				
		if($this->includeITunes) {
		    $rss['itunes'] = $itunes;
		}
				
		//Zend_Debug::dump($rss);die;
		
		// import the array
		$feed = Zend_Feed::importArray($rss, 'rss');

		// write the feed to a variable
		$rssFeed = $feed->saveXml();
		
		// Write the feed to a file residing in /public/rss
		//Zend_Debug::dump(Zend_Registry::get('rssConfig')->rssFilePath . DIRECTORY_SEPARATOR . $this->resource . '.xml');die;
		$fh = fopen(Zend_Registry::get('rssConfig')->rssFilePath . DIRECTORY_SEPARATOR . $this->resource . '.xml', "w");
		fwrite($fh, $rssFeed);
		fclose($fh);
        
    }
    
    /**
     * A copy of the SMarty truncate function
     *
     * @param text $string
     * @param int $length
     * @param string $etc
     * @param bool $break_words
     * @param bool $middle
     * @return string
     */
    public function _truncate($string, $length = 80, $etc = '...',
                                      $break_words = false, $middle = false)
    {
        if ($length == 0)
            return '';
    
        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
            }
            if(!$middle) {
                return substr($string, 0, $length) . $etc;
            } else {
                return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
            }
        } else {
            return $string;
        }
    }
}
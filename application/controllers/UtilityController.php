<?php
/**
 * Yehoodi 3.0 UtilityController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * A utility class for various site wide utilities
 *
 */
    
class UtilityController extends CustomControllerAction
{
    public function captchaAction()
    {
        $session = new Zend_Session_Namespace('captcha');

        // check for existing phrase in session
        $phrase = null;
        if (isset($session->phrase) && strlen($session->phrase) > 0)
            $phrase = $session->phrase;

        $captcha = Text_CAPTCHA::factory('Image');

        $opts = array('font_size' => 20,
                      'font_path' => Zend_Registry::get('smartyConfig')->paths->data,
                      'font_file' => 'VeraBd.ttf');

        $captcha->init(120, 60, $phrase, $opts);

        // write the phrase to session
        $session->phrase = $captcha->getPhrase();

        // disable auto-rendering since we're outputting an image
        $this->_helper->viewRenderer->setNoRender();

        header('Content-type: image/png');
        echo $captcha->getCAPTCHAAsPng();
    }

    /**
     * Handles outputting images
     *
     */
    public function imageAction()
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $id = (int) $request->getQuery('id');
        $w  = (int) $request->getQuery('w');
        $h  = (int) $request->getQuery('h');
        $hash = $request->getQuery('hash');

        $realHash = DatabaseObject_ResourceImage::GetImageHash($id, $w, $h);

        // disable autorendering since we're outputting an image
        $this->_helper->viewRenderer->setNoRender();

        $image = new DatabaseObject_ResourceImage($this->db);
        if ($hash != $realHash || !$image->load($id)) {
            // image not found
            $response->setHttpResponseCode(404);
            return;
        }

        try {
            $fullpath = $image->createThumbnail($w, $h);
        }
        catch (Exception $ex) {
            $fullpath = $image->getFullPath();
        }

        $info = getImageSize($fullpath);

        $response->setHeader('content-type', $info['mime']);
        $response->setHeader('content-length', filesize($fullpath));
        echo file_get_contents($fullpath);
    }

    /**
     * Handles outputting images
     * for resource thumbnails
     *
     */
    public function resourcethumbnailAction()
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $id = (int) $request->getQuery('id');
        $w  = (int) $request->getQuery('w');
        $h  = (int) $request->getQuery('h');
        $hash = $request->getQuery('hash');

        $realHash = DatabaseObject_ResourceImage::GetImageHash($id, $w, $h);

        // disable autorendering since we're outputting an image
        $this->_helper->viewRenderer->setNoRender();

        $image = new DatabaseObject_ResourceImage($this->db);
        if ($hash != $realHash || !$image->load($id)) {
            // image not found
            $response->setHttpResponseCode(404);
            return;
        }

        try {
            $fullpath = $image->createResourceThumbnail($w, $h);
        }
        catch (Exception $ex) {
            $fullpath = $image->getFullPath();
        }

        $info = getImageSize($fullpath);

        $response->setHeader('content-type', $info['mime']);
        $response->setHeader('content-length', filesize($fullpath));
        echo file_get_contents($fullpath);
    }

    /**
     * Handles outputting preview images
     * for the submit page
     * 
     * Refer to revision 1156 for the old
     * thumbnail version of this method.
     * 
     * This steals from the createThumbnail() method 
     * of the ResourceImage.php object.
     * 
     */
    public function imagepreviewAction()
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $tempFilename = $request->getQuery('tempFilename');
        $maxW  = (int) $request->getQuery('w');
        $maxH  = (int) $request->getQuery('h');

        // disable autorendering since we're outputting an image
        $this->_helper->viewRenderer->setNoRender();

        $preview = Zend_Registry::get('imageConfig')->tempImagePath . DIRECTORY_SEPARATOR . $tempFilename;

		$fullpath = $preview;

            $ts = (int) filemtime($fullpath);
            $info = getImageSize($fullpath);

            $w = $info[0];          // original width
            $h = $info[1];          // original height

            $ratio = $w / $h;       // width:height ratio

            $maxW = min($w, $maxW); // new width can't be more than $maxW
            if ($maxW == 0)         // check if only max height has been specified
                $maxW = $w;

            $maxH = min($h, $maxH); // new height can't be more than $maxH
            if ($maxH == 0)         // check if only max width has been specified
                $maxH = $h;

            $newW = $maxW;          // first use the max width to determine new
            $newH = $newW / $ratio; // height by using original image w:h ratio

            if ($newH > $maxH) {        // check if new height is too big, and if
                $newH = $maxH;          // so determine the new width based on the
                $newW = $newH * $ratio; // max height
            }

            if ($w == $newW && $h == $newH) {
                // no thumbnail required, just return the original path
                $fullpath = $preview;
            } else {

	            switch ($info[2]) {
	                case IMAGETYPE_GIF:
	                    $infunc = 'ImageCreateFromGif';
	                    $outfunc = 'ImageGif';
	                    break;
	
	                case IMAGETYPE_JPEG:
	                    $infunc = 'ImageCreateFromJpeg';
	                    $outfunc = 'ImageJpeg';
	                    break;
	
	                case IMAGETYPE_PNG:
	                    $infunc = 'ImageCreateFromPng';
	                    $outfunc = 'ImagePng';
	                    break;
	
	                default;
	                    throw new Exception('Invalid image type');
	            }
	
	            // create a unique filename based on the specified options
	            $filename = sprintf('%d.%dx%d.%d',
	                                rand(1,999999),
	                                $newW,
	                                $newH,
	                                $ts);
	
	            // autocreate the directory for storing thumbnails
	            $path = Zend_Registry::get('imageConfig')->tempImagePath;
	            if (!file_exists($path))
	                mkdir($path, 0777);
	
	            if (!is_writable($path))
	                throw new Exception('Unable to write to thumbnail dir');
	
	            // determine the full path for the new thumbnail
	            $thumbPath = sprintf('%s'.DIRECTORY_SEPARATOR.'%s', $path, $filename);
	
	            if (!file_exists($thumbPath)) {
	
	                // read the image in to GD
	                $im = @$infunc($fullpath);
	                if (!$im)
	                    throw new Exception('Unable to read image file');
	
	                // create the output image
	                $thumb = ImageCreateTrueColor($newW, $newH);
	
	                // now resample the original image to the new image
	                ImageCopyResampled($thumb, $im, 0, 0, 0, 0, $newW, $newH, $w, $h);
	
	                $outfunc($thumb, $thumbPath);
	            }
	
	            if (!file_exists($thumbPath)) {
	                throw new Exception('Unknown error occurred creating thumbnail');
	            }
	            
	            if (!is_readable($thumbPath)) {
	                throw new Exception('Unable to read thumbnail');
	            }
		        
	            $fullpath = $thumbPath;
            }
            

        $info = getImageSize($fullpath);

        $response->setHeader('content-type', $info['mime']);
        $response->setHeader('content-length', filesize($preview));
        echo file_get_contents($fullpath);
    }

    /**
     * Handles outputting avatars
     * If no avatar is found it uses the
     * image in the config.ini
     *
     */
    public function avatarAction()
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $id = (int) $request->getQuery('id');
        $w  = (int) $request->getQuery('w');
        $h  = (int) $request->getQuery('h');
        $hash = $request->getQuery('hash');

        $realHash = DatabaseObject_ResourceImage::GetImageHash($id, $w, $h);

        // disable autorendering since we're outputting an image
        $this->_helper->viewRenderer->setNoRender();

        $image = new DatabaseObject_UserAvatar($this->db);
        if ($hash != $realHash) {
            // invalid hash
            $response->setHttpResponseCode(404);
            return;
        }
        
        if (!$image->load($id)) {
            // image not found
            $fullpath = Y3ROOT_PATH . 'public' . Zend_Registry::get('userConfig')->DefaultAvatar;
            $info = getImageSize($fullpath);
        } else {
            $fullpath = $image->getFullPath();
            $info = getImageSize($fullpath);
        }

        if ($info) {
            $response->setHeader('content-type', $info['mime']);
            $response->setHeader('content-length', filesize($fullpath));
            echo file_get_contents($fullpath);
        }
    }

	public static function cleanUpUserName($text) {
	
		// strip the html and slashes
		$text = trim(htmlspecialchars(stripslashes($text)));
		
		// Clean the comments with this regex
		$filters = array(
		    // replace & with 'and' for readability
		    '/&+/' => 'and',

		    // replace non-alphanumeric characters with a space
		    '/[^a-z0-9]+/i' => ' ',
		
		    // replace multiple spaces with a single space
		    '/ +/'          => ' '
		);

		// This is weird. Why can't I get this in the damn RegEx?
		$text = str_replace('!','',$text);
		
		// apply each replacement
		foreach ($filters as $regex => $replacement)
		    $cleanText = preg_replace($regex, $replacement, $text);
		    
		//Zend_Debug::dump($cleanText);die;
		return $cleanText;
	}

	/**
	 * Cleans the title of a resource for
	 * solr
	 *
	 * @param string $text
	 * @return string
	 */
	public static function cleanupTitle($text) {
		$text = html_entity_decode($text);
	
		// Strip the html
		$text = strip_tags($text);
		
		// Finally, kill anything thats not alphanumeric
		//$text = preg_replace('/[^\w\s]/', ' ', $text);
	
		return $text;
	}
	
	/**
	 * Simple dirty word filter
	 * User can turn this off if they like
	 *
	 * @param string $text
	 * @return string
	 */
	public static function cleanDirtyWords($text)
	{
        $words = array('shit', 'pussy', 'fuck', 'motherfucker', 'nigger', 'cocksucker', 'tits', 'cunt', 'cock ', 'cock.', 'cock?', 'cock!');
		$tmp = $text;
        while (1) {
            // Try and replace an occurrence of swear words:
            $text = str_ireplace($words,
            					'[bleep!]',
                                 $text);

            // If nothing changed this iteration then break the loop
            if ($text == $tmp)
                break;

            $tmp = $text;
        }

        return $text;
	}

	/**
	 * gets the distance between two lat's and long's
	 *
	 * @param string $latitude1
	 * @param string $longitude1
	 * @param string $latitude2
	 * @param string $longitude2
	 * @param string $unit
	 * @return int distance
	 */
	public static function getHaversineDistance($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'mi')
	{
		$theta = $longitude1 - $longitude2;
		$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) +
			(cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) *
			cos(deg2rad($theta)));
		$distance = acos($distance);
		$distance = rad2deg($distance);
		$distance = $distance * 60 * 1.1515;
	
		switch($unit) {
			case 'mi':
				break;
		
			case 'km':
				$distance = $distance * 1.609344;
		}
		return (round($distance,2));
	}

    /**
     * Clean up stuff from the outside world
     *
     * Note: This is stolen from the FromProcessor
     *
     * @param string $value
     * @return string
     */
	public static function sanitize($value)
    {
        if (!$value instanceof Zend_Filter) {
            $sanitizeChain = new Zend_Filter();
            $sanitizeChain->addFilter(new Zend_Filter_StringTrim())
                                 ->addFilter(new Zend_Filter_StripTags());
        }

        // filter out any line feeds / carriage returns
        $ret = preg_replace('/[\r\n]+/', ' ', $value);

        // filter using the above chain

        return $sanitizeChain->filter($ret);
    }
}
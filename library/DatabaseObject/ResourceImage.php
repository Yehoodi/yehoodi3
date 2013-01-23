<?php
    class DatabaseObject_ResourceImage extends DatabaseObject
    {

    	protected $_uploadedFile;

    	public function __construct($db)
        {
            parent::__construct($db, 'resource_image', 'image_id');

            // These are required
            $this->add('filename');
            $this->add('rsrc_id');
            $this->add('caption');
        }
                
        public function preInsert()
        {
            // first check that we can write the upload directory
            $path = self::GetUploadPath();
            if (!file_exists($path) || !is_dir($path))
                throw new Exception('Upload path ' . $path . ' not found');

            if (!is_writable($path))
                throw new Exception('Unable to write to upload path ' . $path);

            return true;
        }
        /**
         * Do this stuff after the object is loaded
         *
         */
        protected function postLoad()
        {
        	
        }//postLoad

        public function postInsert()
        {
            //Zend_Debug::dump($this->_uploadedFile);
            //Zend_Debug::dump($this->getFullPath());die;
        	if (strlen($this->_uploadedFile) > 0)
                return move_uploaded_file($this->_uploadedFile, $this->getFullPath());

            return false;
        }

        protected function postUpdate()
        {
        	return true;
        }

        public function preDelete()
        {
            // Delete the resource image associated
        	unlink($this->getFullPath());

            $pattern = sprintf('%s/%d.*',
                               self::GetThumbnailPath(),
                               $this->getId());

            foreach (glob($pattern) as $thumbnail) {
                unlink($thumbnail);
            }

            return true;
        }

        /**
         * Gets the caption for this image
         * (if any)
         *
         * @return string
         */
        public function getCaption()
        {
            return $this->caption;
        }
        
        /**
         * Allow only the logged-in user
         * to delete images on her resource
         *
         * @param int $rsrc_id
         * @param int $image_id
         * @return bool (true/false)
         */
        public function loadForResource($rsrc_id, $image_id)
        {
            $rsrc_id = (int) $rsrc_id;
            $image_id = (int) $image_id;

            if ($rsrc_id <= 0 || $image_id <= 0)
                return false;

            $query = sprintf(
                'select %s from %s where rsrc_id = %d and image_id = %d',
                join(', ', $this->getSelectFields()),
                $this->_table,
                $rsrc_id,
                $image_id
            );

            return $this->_load($query);
        }

        /**
         * Returns the path on the filesystem where
         * uploaded images are stored
         *
         * @return path string
         */
        public static function GetUploadPath()
        {
            $config = Zend_Registry::get('imageConfig');

            return $config->resourceImagePath;
        }
        
        /**
         * Returns the full path and the
         * uploaded file name id
         *
         * @return path string
         */
        public function getFullPath()
        {
        	return sprintf("%s".DIRECTORY_SEPARATOR."%d", self::GetUploadPath(), $this->getId());
        }

        /**
         * Stores the temp path of the uploaded file
         * in anticipation of the save() method
         *
         * @param unknown_type $path
         */
        public function uploadFile($path)
        {
            if (!file_exists($path) || !is_file($path))
                throw new Exception('Unable to find uploaded file');

            if (!is_readable($path))
                throw new Exception('Unable to read uploaded file');

            $this->_uploadedFile = $path;
        }

        /**
         * Returns the full path for
         * thumbnail storage
         *
         * @return string path
         */
        public static function GetThumbnailPath()
        {
        	$config = Zend_Registry::get('imageConfig');
        	
        	return $config->thumbnailImagePath;
        }
        
        /**
         * Generates thumbnails for images
         *
         * @param unknown_type $maxW
         * @param unknown_type $maxH
         */
        public function createThumbnail($maxW, $maxH)
        {
            $fullpath = $this->getFullpath();

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
                return $fullpath;
            }

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
                                $this->getId(),
                                $newW,
                                $newH,
                                $ts);

            // autocreate the directory for storing thumbnails
            $path = self::GetThumbnailPath();
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

            if (!file_exists($thumbPath))
                throw new Exception('Unknown error occurred creating thumbnail');
            if (!is_readable($thumbPath))
                throw new Exception('Unable to read thumbnail');

            return $thumbPath;
        }

        /**
         * Generates thumbnails for Resources
         * Always the same size and cropped
         *
         * @param unknown_type $maxW
         * @param unknown_type $maxH
         */
        public function createResourceThumbnail()
        {
            $fullpath = $this->getFullpath();

            $ts = (int) filemtime($fullpath);
            $info = getImageSize($fullpath);

            $w = $info[0];          // original width
            $h = $info[1];          // original height

			$new_w = 100;
			$new_h = 100;
			
			if ( $w > $h ) {
				$x = ceil( ($w - $h) / 2);
				$w = $h;
			} elseif ( $h > $w ) {
				$y = ceil( ($h - $w) / 2 );
				$h = $w;
			}
			
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
                                $this->getId(),
                                $w,
                                $h,
                                $ts);

            // autocreate the directory for storing thumbnails
            $path = self::GetThumbnailPath();
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
                $thumb = imagecreatetruecolor($new_w, $new_h);

                // now resample the original image to the new image
                imagecopyresampled($thumb, $im, 0, 0, $x, $y, $new_w, $new_h, $w, $h);

                $outfunc($thumb, $thumbPath);
            }

            if (!file_exists($thumbPath))
                throw new Exception('Unknown error occurred creating thumbnail');
            if (!is_readable($thumbPath))
                throw new Exception('Unable to read thumbnail');

            return $thumbPath;
        }
        
        /**
         * Generates a hash that is checked
         * later to defend against users
         * trying to access images through
         * the url
         *
         * @param int $id
         * @param int $w
         * @param int $h
         * @return md5 string
         */
        public static function GetImageHash($id, $w, $h)
        {
            $id = (int) $id;
            $w  = (int) $w;
            $h  = (int) $h;

            return md5(sprintf('%s,%s,%s', $id, $w, $h));
        }
        
        /**
         * Retreives Images
         *
         * @param unknown_type $db
         * @param unknown_type $options
         * @return unknown
         */
        public static function GetImages($db, $options = array())
        {
            // initialize the options
            $defaults = array('rsrc_id' => array());

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = $db->select();
            $select->from(array('i' => 'resource_image'), array('i.*'));

            // filter results on specified post ids (if any)
            if (count($options['rsrc_id']) > 0)
                $select->where('i.rsrc_id in (?)', $options['rsrc_id']);

            //$select->order('i.ranking');

            // fetch post data from database
            $data = $db->fetchAll($select);

            // turn data into array of DatabaseObject_BlogPostImage objects
            $images = parent::BuildMultiple($db, __CLASS__, $data);

            return $images;
        }

        
        /**
         * Attempts to load an image_id by a given rsrc_id
         *
         * @param int $user_id
         * @param int $post_id
         * @return image_id int or FALSE
         */
        public static function loadImageId($db, $rsrc_id)
        {
            $rsrc_id = (int) $rsrc_id;

            if ($rsrc_id <= 0)
                return false;

             // instantiate a Zend select object
            $select = $db->select();
            $select->from('resource_image', 'image_id')
            	   ->where('rsrc_id = ?', $rsrc_id);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

        public function sendLive() {
        	if ($this->is_active != self::STATUS_LIVE ) {
        		$this->is_active = self::STATUS_LIVE ;
        		$this->rsrc_date = $this->dateTime->format("Y-m-d H:i:s");
        	}
        }

/*		public static function deleteResourceImage($db, $id)
		{
        	// remove the associated data from the resource_image table
        	if (!$db->delete(Zend_Registry::get('dbTableConfig')->tblResourceImage, "rsrc_id = " . $id))
        		return FALSE;
        		
			// Delete from the filesystem
			unlink(Zend_Registry::get('imageConfig')->resourceImagePath.DIRECTORY_SEPARATOR.$this->image->getId()))
			
			// Delete from the session
			$imagePreview = new Zend_Session_Namespace('submitPreview');
			unset($imagePreview->filename);
			unset($imagePreview->tempFilename);


        	return TRUE;
		}
*/    }
<?php
    /**
     * handles uploading resource image previews
     *
     */
    class FormProcessor_ResourceImagePreview extends FormProcessor
    {
        //public $resource;
        public $image;
        public $filename;
        public $tempFilename;
        public $caption;

        public function process(Zend_Controller_Request_Abstract $request)
        {
            if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
                $this->addError('image', 'Invalid upload data');
                return false;
            }

            $file = $_FILES['image'];

            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    // success
                    break;

                case UPLOAD_ERR_FORM_SIZE:
                    // only used if MAX_FILE_SIZE specified in form
                case UPLOAD_ERR_INI_SIZE:
                    $this->addError('image', 'The uploaded file was too large');
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $this->addError('image', 'File was only partially uploaded');
                    break;

                case UPLOAD_ERR_NO_FILE:
                    $this->addError('image', 'No file was uploaded');
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->addError('image', 'Temporary folder not found');
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    $this->addError('image', 'Unable to write file');
                    break;

                case UPLOAD_ERR_EXTENSION:
                    $this->addError('image', 'Invalid file extension');
                    break;

                default:
                    $this->addError('image', 'Unknown error code');
            }

            if ($this->hasError())
                return false;

            $info = getimagesize($file['tmp_name']);
            if (!$info) {
                $this->addError('type', 'Uploaded file was not an image');
                return false;
            }

            switch ($info[2]) {
                case IMAGETYPE_PNG:
                case IMAGETYPE_GIF:
                case IMAGETYPE_JPEG:
                    break;

                default:
                    $this->addError('type', 'Invalid image type uploaded');
                    return false;
            }
            // if no errors have occurred, save the image
            if (!$this->hasError()) {
                $this->uploadFile($file['tmp_name']);
                $this->filename = basename($file['name']);
                $this->tempFilename = md5(time().basename($file['name']));
                if($this->uploadCheck())
                	$this->moveToTemp();
            	//Zend_Debug::dump($this);die;
                //$this->image->save();
            }

            return !$this->hasError();
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
         * Returns the path on the filesystem where
         * uploaded images are stored
         *
         * @return path string
         */
        public static function GetUploadPath()
        {
            $config = Zend_Registry::get('imageConfig');

            return $config->tempImagePath;
        }

        /**
         * Returns the full path and the
         * uploaded file name
         *
         * @return path string
         */
        public function getFullPath()
        {
        	//Zend_Debug::dump($this->filename);die;
        	//echo sprintf('%s'.DIRECTORY_SEPARATOR.'%s', self::GetUploadPath(), $this->filename);die;
        	return sprintf('%s'.DIRECTORY_SEPARATOR.'%s', self::GetUploadPath(), $this->tempFilename);
        }

        public function uploadCheck()
        {
            // first check that we can write the upload directory
            $path = self::GetUploadPath();
            if (!file_exists($path) || !is_dir($path))
                throw new Exception('Upload path ' . $path . ' not found');

            if (!is_writable($path))
                throw new Exception('Unable to write to upload path ' . $path);

            return true;
        }

        public function moveToTemp()
        {
            //Zend_Debug::dump($this->filename);die;
            //Zend_Debug::dump($this->getFullPath());die;
        	if (strlen($this->_uploadedFile) > 0)
                return move_uploaded_file($this->_uploadedFile, $this->getFullPath());

            return false;
        }

         /**
         * Returns the full path for
         * temp submit page thumbnail 
         * storage
         *
         * @return string path
         */
        public static function GetTempThumbnailPath()
        {
        	$config = Zend_Registry::get('imageConfig');
        	
        	return $config->tempThumbnailImagePath;
        }

        /**
         * Generates thumbnails for images
         *
         * @param unknown_type $maxW
         * @param unknown_type $maxH
         */
        public function createTempThumbnail($file, $maxW, $maxH)
        {
            //$fullpath = $this->getFullpath();
            $fullpath = Zend_Registry::get('imageConfig')->tempImagePath.DIRECTORY_SEPARATOR.$file;
            //Zend_Debug::dump($fullpath);die;

            $ts = (int) filemtime($fullpath);
            $info = getImageSize($fullpath);

            $w = $info[0];          // original width
            $h = $info[1];          // original height

			$new_w = 75;
			$new_h = 75;
			
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
                                $this->filename,
                                $w,
                                $h,
                                $ts);

            // autocreate the directory for storing thumbnails
            $path = self::GetTempThumbnailPath();
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

    }
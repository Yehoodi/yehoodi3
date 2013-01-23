<?php
    class DatabaseObject_UserAvatar extends DatabaseObject
    {

    	protected $_uploadedFile;
    	public $avatar;

    	/**
    	 * Set up the fields for the avatar
    	 *
    	 * @param unknown_type $db
    	 */
    	public function __construct($db)
        {
            parent::__construct($db, 'user_avatar', 'avatar_id');

            // These are required
            $this->add('filename');
            $this->add('user_id');
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

        protected function postLoad()
        {

        }

        public function postInsert()
        {
			// this actually moves the file from temp location to actual location
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

            return true;
        }

        /**
         * Static method:
         * Attempts to load an avatar_id by a given user_id
         *
         * @param int $user_id
         * @param int $avatar_id
         * @return image_id int or FALSE
         */
        public static function loadAvatarId($db, $userId)
        {
            $userId = (int) $userId;

            if ($userId <= 0)
                return false;

             // instantiate a Zend select object
            $select = $db->select();
            $select->from('user_avatar', 'avatar_id')
            	   ->where('user_id = ?', $userId);

            //Zend_Debug::dump($select->__toString());die;
            return $db->fetchOne($select);
        }

         /**
         * Allow only the logged-in user
         * to delete her avatars
         *
         * @param int $user_id
         * @param int $avatar_id
         * @return bool
         */
        public function loadAvatarForUser($user_id, $avatar_id)
        {
            $user_id = (int) $user_id;
            $avatar_id = (int) $avatar_id;

            if ($user_id <= 0 || $avatar_id <= 0)
                return false;

            $query = sprintf(
                'select %s from %s where user_id = %d and avatar_id = %d',
                join(', ', $this->getSelectFields()),
                $this->_table,
                $user_id,
                $avatar_id
            );

            return $this->_load($query);
        }

       /**
         * Returns the path on the filesystem where
         * avatar images are stored
         *
         * @return path string
         */
        public static function GetUploadPath()
        {
            $config = Zend_Registry::get('imageConfig');

            return $config->AvatarPath;
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
         * Stores the temp path of the uploaded avatar file
         * in anticipation of the save() method
         *
         * @param string $path
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
    }
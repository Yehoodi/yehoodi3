<?php
    /**
     * handles uploading resource images
     *
     */
    class FormProcessor_UserAvatar extends FormProcessor
    {
        public $user;
        public $avatar;

        public function __construct($db, $user_id)
        {
           parent::__construct();

            $this->db = $db;
            
            // instantiate new user
            $this->user = new DatabaseObject_User($db);
            $this->user->load($user_id);

            // Instantiate new avatar object
            $this->avatar = new DatabaseObject_UserAvatar($db);
			$avatar_id = DatabaseObject_UserAvatar::loadAvatarId($db, $user_id);
			
			$this->avatar->load($avatar_id);
			$this->avatar->user_id = $this->user->getId();

        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            if (!isset($_FILES['avatar']) || !is_array($_FILES['avatar'])) {
                $this->addError('avatar', 'Invalid upload data');
                return false;
            }

            $file = $_FILES['avatar'];
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    // success
                    break;

                case UPLOAD_ERR_FORM_SIZE:
                    // only used if MAX_FILE_SIZE specified in form
                case UPLOAD_ERR_INI_SIZE:
                    $this->addError('avatar', 'The uploaded file was too large');
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $this->addError('avatar', 'File was only partially uploaded');
                    break;

                case UPLOAD_ERR_NO_FILE:
                    $this->addError('avatar', 'No file was uploaded');
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->addError('avatar', 'Temporary folder not found');
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    $this->addError('avatar', 'Unable to write file');
                    break;

                case UPLOAD_ERR_EXTENSION:
                    $this->addError('avatar', 'Invalid file extension');
                    break;

                default:
                    $this->addError('avatar', 'Unknown error code');
            }

            if ($this->hasError())
                return false;

            $info = getimagesize($file['tmp_name']);
            if (!$info) {
                $this->addError('avatar', 'Uploaded file was not an image');
                return false;
            }

            switch ($info[2]) {
                case IMAGETYPE_PNG:
                case IMAGETYPE_GIF:
                case IMAGETYPE_JPEG:
                    break;
                    
                default:
                    $this->addError('avatar', 'Invalid image type uploaded');
                    return false;
            }
            
            // Avatar size check
            if ($info[0] > 80 || $info[1] > 80 ) {
                $this->addError('avatar', 'Uploaded file must be 80x80 pixels or less');
                return false;
            }
            
            // if no errors have occurred, save the image
            if (!$this->hasError()) {
                $this->avatar->uploadFile($file['tmp_name']);
                $this->avatar->filename = basename($file['name']);
                $this->avatar->save();
            }

            return !$this->hasError();
        }
    }
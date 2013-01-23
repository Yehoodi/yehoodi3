<?php

/**
 * This class performs the versioning needed
 * during a push to production so the users
 * browser loads the newest version of .css
 * and .js files.
 *
 */
class version {
    
    protected $_filename = null;
    protected $_filenameContents = null;
    protected $_timestamp = null;
    
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        // Init stuff
        $this->_timestamp = time();
    }
    
    /**
     * Loads the specified file for updating
     *
     * @param string $filename
     */
    public function loadFile($filename = null) {
        if (!$filename) {
            throw new Exception('No filename specified');
        }
        
        $this->_filename = $filename;
        $this->_fileContents = file_get_contents($this->_filename);
    }
    
    /**
     * Appends all occurances of .css
     * with the ?v={timestamp} required for
     * version information.
     *
     */
    public function versionCSS() {
        
        $extension = '.css';
        $this->_fileContents = str_replace($extension, $extension . '?v=' . $this->_timestamp, $this->_fileContents);
    }
    
    /**
     * Appends all occurances of {$jsExt}
     * with the ?v={timestamp} required for
     * version information.
     *
     */
    public function versionJS() {
        
        $extension = '{$version}';
        $this->_fileContents = str_replace($extension, '?v=' . $this->_timestamp, $this->_fileContents);
    }
    
    /**
     * Saves the updated file
     *
     */
    public function saveFile() {
        
        //var_dump($this->_fileContents);
        file_put_contents($this->_filename, $this->_fileContents);
    }
}
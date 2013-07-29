<?php
/*
Sample usage:

$upload = new Uploader("myfile");
$upload->maxFileSize(8*1024*1024) // limits the file size to 1MB
       ->allowOverride() // allows the target file to be overridden
       ->allowMimeType("image/jpg", "image/png") // allows jpg and png files
       ->save("test.png");
if (!$uploader) var_dump($uploader->_errors);
*/

if (!defined("UPLOADER_DEFAULT_DIR")) {
    trigger_error("Upload directory not set");
    define("UPLOADER_DEFAULT_DIR", __DIR__ . DIRECTORY_SEPARATOR . "uploads");
}
if (!defined("UPLOADER_MAX_FILE_SIZE")) {
    define("UPLOADER_MAX_FILE_SIZE", 1024*1024*8);
}

class Uploader {
    
    
    /**
     * The file
     */
    public $file = null;
    
    
    /**
     * Error stack
     */
    public $_errors = array();
    
    
    /**
     * Allows the file to be overridden
     */
    private $_allowOverride = false;
    
    
    /**
     * Wheter to prepend a unique string to the filename
     */
    private $_makeUnique = false;
    
    
    /**
     * Store the filepath
     */
    public $filepath = "";
    
    
    /**
     * Store the file name
     */
    public $filename = "";
    
    
    /**
     * Constructor
     *
     * @param String Input $file file name
     * @param Array $source Optional source of file info. Defaults to global files array
     */
    public function __construct ($file, $source = null) {
        if ($source === null) $source = $_FILES;
        if (empty($source[$file])) $this->pushError("File does not exist");
        
        $this->file = $source[$file];
        $this->copyFileErrors();
        
    }
    
    
    /**
     * Adds an error to the stack
     *
     * @param String $error
     */
    private function pushError ($error) {
        $this->_errors[] = $error;
        return $this;
    }
    
        
    /**
     * Copies file error to internal stack
     */
    private function copyFileErrors () {
        if (!$this->file) return; 
        if ($this->file["error"]) $this->pushError($this->file["error"]);        
    }
    
    
    /**
     * Checks the file size
     * 
     * @param Integer $size Optional maximum file size in byte
     */
    public function maxFileSize ($size = UPLOADER_MAX_FILE_SIZE) {
        if (!$this->file) return $this;
        
        if ($this->file["size"] > $size)
            $this->pushError("File size limited to " . $size/1024/1024/8 . "MB");
        
        return $this;
    }
    
    
    /**
     * Gets mime type
     *
     * @param String $path Filepath
     * @TODO: BLAAA finfo
     */
    public function getMimeType ($path) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo,$path);
        finfo_close($finfo);
        
        $mime = current(explode(";", $type));
        return $mime;
    }
    
    
    /**
     * Allows mime types
     *
     * @param String $type Allowed mime type/s
     */
    public function allowMimeType () {
        if (!$this->file) return $this;
        
        $mimeTypes = func_get_args();
        $argNum = func_num_args();
        
        if ($argNum == 0) return $this;
        
        // see if we matched the mime type
        $mimeType = $this->getMimeType($this->file["tmp_name"]);
        $matched = in_array($mimeType, $mimeTypes);
        
        
        if (!$matched) $this->pushError("Invalid MIME type. Allowed types are: " . implode(", ", $mimeTypes) . ". Got " . $mimeType);
            
            
        return $this;
    }
    
    
    /**
     * Allows the file to be overridden
     *
     * @param Boolean $bool
     */
    public function allowOverride ($bool = true) {
        $this->_allowOverride = $bool;
        return $this;
    }
    
    
    /**
     * Adds a unique string to the file name
     */
    public function makeUnique () {
    	$this->_makeUnique = true;
    	return $this;
    }
    
    
    /**
     * Checks if the file already exists
     *
     * @param String $dir
     * @param String $file
     */
    public function fileExists ($dir = UPLOADER_DEFAULT_DIR, $fileName = null) {
        return file_exists($dir . DIRECTORY_SEPARATOR . $fileName);
    }
    
    
    /**
     * Checks the file name
     *
     * @param String $name Filename
     */
    private function checkFileName ($name) {
        $errmsg = "Invalid file name";
        if (strlen($name) > 255)
            return $this->pushError($errmsg);
        
        if (preg_match("/[^a-zA-Z0-9_\-\.%]/", $name))
            return $this->pushError($errmsg);
    }
    
    
    /**
     * Saves the file
     *
     * @param String $filename Name the file will be stored as
     * @param String $dir Optinal Dir the file will be saved to
     */
    public function save ($filename = null, $dir = UPLOADER_DEFAULT_DIR) {
        if (!$this->file) return false;
        
        $this->filename = str_replace(" ", "_", $filename ? $filename : $this->file["name"]);
        if ($this->_makeUnique) $this->filename = uniqid("") . $this->filename;
        $this->checkFileName($this->filename);

        if ($this->_allowOverride == false && $this->fileExists($dir, $this->filename)) 
            $this->pushError("File already exists");
        
        if (sizeof($this->_errors) > 0) return false;
        
        $this->filepath = $dir . DIRECTORY_SEPARATOR . $this->filename;
        $ok = move_uploaded_file($this->file["tmp_name"], $this->filepath);
        if (!$ok) $this->pushError("Something went wrong");
        
        return $ok;
    }
}
<?php

class Image {
	
	
	/**
	 * File format jpg
	 */
	const jpg = "image/jpeg";
	
	
	/**
	 * File format png
	 */
	const png = "image/png";
	
	
	/**
	 * Stores meta data
	 */
	private $_meta;
	
	
	/**
	 * Errors
	 */
	public $_errors = array();
	
	
	/**
	 * Stores actual source image
	 */
	public $source;
	
	
	/**
	 * Stores destination image
	 */
	public $image;
	
	
	/**
	 * Stores source image location
	 */
	public $sourceFile = "";
	
	
	/**
	 * Target file format
	 */
	public $targetFormat = "";
	
	
	/**
	 * Constructor
	 *
	 * @param String $source
	 */
	public function __construct ($source = "") {
		$this->sourceFile   = $source;
		if (($this->_meta   = getimagesize($this->sourceFile)) === null) return $this->pushError("Invalid file");
		
		$this->targetFormat = $this->getMimeType();
		$this->source       = $this->sourceFileToImage();
	}
	
	
	/**
	 * Destructor
	 *
	 * Destroys the image reference
	 */
	public function __destruct () {
		if ($this->image)  imagedestroy($this->image);
		if ($this->source) imagedestroy($this->source);
	}
	
	
	/**
	 * Sets an error
	 */
	public function pushError ($msg) {
		$this->_errors[] = $msg;
	}
	
	
	/**
	 * Set format to convert to
	 *
	 * @param String $format
	 */
	public function toFormat ($format) {
		$this->targetFormat = $format;
	}
	
	
	/**
	 * Gets the mime type
	 *
	 * @return String
	 */
	public function getMimeType () {
		return $this->_meta["mime"];
	}
	
	
	/**
	 * Gets source file width
	 *
	 * @return String
	 */
	public function getSourceWidth () {
		return $this->_meta[0];
	}
	
	
	/**
	 * Gets source file height
	 *
	 * @return String
	 */
	public function getSourceHeight () {
		return $this->_meta[1];
	}
	
	
	/**
	 * Creates image from type
	 *
	 * @param String $type
	 * @return Image
	 */
	private function sourceFileToImage () {
		return imagecreatefromstring(file_get_contents($this->sourceFile));
	}
	
	
	/**
	 * Crops an Image
	 * 
	 * @param Int $width
	 * @param Int $heigth Optional
	 * @param Int $x Optional
	 * @param Int $y Optional
	 */
	public function crop ($width, $height = null, $x = 0, $y = 0) {
		if ($height === null) $height = $width;
		
		$sourceWidth  = $this->getSourceWidth();
		$sourceHeight = $this->getSourceHeight();
		
		if ($sourceWidth / $sourceHeight >= $width/$height) 
		{
			$targetHeight = $height;
			$targetWidth  = $sourceWidth / ($sourceHeight / $height);
		}
		else 
		{
			$targetWidth  = $width;
			$targetHeight = $sourceHeight / ($sourceWidth / $width);
		}
		
		$this->image = imagecreatetruecolor($width, $height);
		imagecopyresampled(
			$this->image,
			$this->source,
			0 - ($targetWidth - $width) / 2,
			0 - ($targetHeight - $height) / 2,
			-$x,
			-$y,
			$targetWidth,
			$targetHeight,
			$sourceWidth,
			$sourceHeight
		);
	}
	
	
	/**
	 * Saves the image
	 *
	 * @param String $dir
	 * @param String $name
	 */
	public function save ($dir, $name) {
		$fileext  = "";
		$filename = $dir . DIRECTORY_SEPARATOR . $name;
		
		switch ($this->targetFormat) {
			case self::jpg :
				$fileext = ".jpg";
				imagejpeg($this->image, $filename . $fileext); 
				break;
			
			default: 
				$this->pushError("Invalid target format"); 
				break;
		}
		
		return $fileext !== "" ? $filename . $fileext : false;
	}
}
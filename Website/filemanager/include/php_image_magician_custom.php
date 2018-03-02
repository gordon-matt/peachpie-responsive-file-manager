<?php

class imageLib {

	private   $fileName;
	private   $image;
	protected $imageResized;
	private   $widthOriginal;     # Always be the original width
	private   $heightOriginal;
	private   $width;         # Current width (width after resize)
	private   $height;
	private   $imageSize;
	private   $fileExtension;

	private $debug      = true;
	private $errorArray = array();

	private $forceStretch        = true;
	private $aggresiveSharpening = false;

	private $transparentArray = array( '.png', '.gif' );
	private $keepTransparency = true;
	private $fillColorArray   = array( 'r' => 255, 'g' => 255, 'b' => 255 );

	private $sharpenArray = array( 'jpg' );

	private $psdReaderPath;
	private $filterOverlayPath;

	private $captionBoxPositionArray = array();

	private $fontDir = 'fonts';

	private $cropFromTopPercent = 10;

	function __construct($fileName)
	{
		if ( ! $this->testGDInstalled())
		{
			if ($this->debug)
			{
				throw new Exception('The GD Library is not installed.');
			}
			else
			{
				throw new Exception();
			}
		};

		$this->initialise();

		// *** Save the image file name. Only store this incase you want to display it
		$this->fileName = $fileName;
		$this->fileExtension = fix_strtolower(strrchr($fileName, '.'));

		// *** Open up the file
		$this->image = $this->openImage($fileName);

		// *** Assign here so we don't modify the original
		$this->imageResized = $this->image;

		// *** If file is an image
		if ($this->testIsImage($this->image))
		{
			// *** Get width and height
			$this->width = imagesx($this->image);
			$this->widthOriginal = imagesx($this->image);
			$this->height = imagesy($this->image);
			$this->heightOriginal = imagesy($this->image);

			/*  Added 15-09-08
             *  Get the filesize using this build in method.
             *  Stores an array of size
             *
             *  $this->imageSize[1] = width
             *  $this->imageSize[2] = height
             *  $this->imageSize[3] = width x height
             *
             */
			$this->imageSize = getimagesize($this->fileName);

		}
		else
		{
			$this->errorArray[] = 'File is not an image';
		}
	}

	private function initialise()
	{
		$this->psdReaderPath = dirname(__FILE__) . '/classPhpPsdReader.php';
		$this->filterOverlayPath = dirname(__FILE__) . '/filters';
	}

	public function resizeImage($newWidth, $newHeight, $option = 0, $sharpen = false, $autoRotate = false)
	{
		$cropPos = 'm';
		if (is_array($option) && fix_strtolower($option[0]) == 'crop')
		{
			$cropPos = $option[1];         # get the crop option
		}
		else
		{
			if (strpos($option, '-') !== false)
			{
				// *** Or pass in a hyphen seperated option
				$optionPiecesArray = explode('-', $option);
				$cropPos = end($optionPiecesArray);
			}
		}

		// *** Check the option is valid
		$option = $this->prepOption($option);

		// *** Make sure the file passed in is valid
		if ( ! $this->image)
		{
			if ($this->debug)
			{
				throw new Exception('file ' . $this->getFileName() . ' is missing or invalid');
			}
			else
			{
				throw new Exception();
			}
		};

		// *** Get optimal width and height - based on $option
		$dimensionsArray = $this->getDimensions($newWidth, $newHeight, $option);

		$optimalWidth = $dimensionsArray['optimalWidth'];
		$optimalHeight = $dimensionsArray['optimalHeight'];

		// *** Resample - create image canvas of x, y size
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
		$this->keepTransparancy($optimalWidth, $optimalHeight, $this->imageResized);
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);

		// *** If '4', then crop too
		if ($option == 4 || $option == 'crop')
		{

			if (($optimalWidth >= $newWidth && $optimalHeight >= $newHeight))
			{
				$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos);
			}
		}

		// *** If Rotate.
		if ($autoRotate)
		{

			$exifData = $this->getExif(false);
			if (count($exifData) > 0)
			{

				switch ($exifData['orientation'])
				{
					case 8:
						$this->imageResized = imagerotate($this->imageResized, 90, 0);
						break;
					case 3:
						$this->imageResized = imagerotate($this->imageResized, 180, 0);
						break;
					case 6:
						$this->imageResized = imagerotate($this->imageResized, -90, 0);
						break;
				}
			}
		}

        //// *** Sharpen image (if jpg and the user wishes to do so)
        //if ($sharpen && in_array($this->fileExtension, $this->sharpenArray))
        //{

        //    // *** Sharpen
        //    $this->sharpen();
        //}
	}

	private function keepTransparancy($width, $height, $im)
    # Author:     Jarrod Oberto
    # Date:       08-04-11
    # Purpose:    Keep transparency for png and gif image
    # Param in:
    # Param out:  n/a
    # Reference:
    # Notes:
    #
	{
		// *** If PNG, perform some transparency retention actions (gif untested)
		if (in_array($this->fileExtension, $this->transparentArray) && $this->keepTransparency)
		{
			imagealphablending($im, false);
			imagesavealpha($im, true);
			$transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
			imagefilledrectangle($im, 0, 0, $width, $height, $transparent);
		}
		else
		{
			$color = imagecolorallocate($im, $this->fillColorArray['r'], $this->fillColorArray['g'], $this->fillColorArray['b']);
			imagefilledrectangle($im, 0, 0, $width, $height, $color);
		}
	}

	private function prepOption($option)
	{
		if (is_array($option))
		{
			if (fix_strtolower($option[0]) == 'crop' && count($option) == 2)
			{
				return 'crop';
			}
			else
			{
				throw new Exception('Crop resize option array is badly formatted.');
			}
		}
		else
		{
			if (strpos($option, 'crop') !== false)
			{
				return 'crop';
			}
		}

		if (is_string($option))
		{
			return fix_strtolower($option);
		}

		return $option;
	}

	public function getExif($debug = false)
	{

		if ( ! $this->debug || ! $debug)
		{
			$debug = false;
		}

		// *** Check all is good - check the EXIF library exists and the file exists, too.
		if ( ! $this->testEXIFInstalled())
		{
			if ($debug)
			{
				throw new Exception('The EXIF Library is not installed.');
			}
			else
			{
				return array();
			}
		};
		if ( ! file_exists($this->fileName))
		{
			if ($debug)
			{
				throw new Exception('Image not found.');
			}
			else
			{
				return array();
			}
		};
		if ($this->fileExtension != '.jpg')
		{
			if ($debug)
			{
				throw new Exception('Metadata not supported for this image type.');
			}
			else
			{
				return array();
			}
		};
		$exifData = exif_read_data($this->fileName, 'IFD0');

		// *** Format the apperture value
		$ev = $exifData['ApertureValue'];
		$apPeicesArray = explode('/', $ev);
		if (count($apPeicesArray) == 2)
		{
			$apertureValue = round($apPeicesArray[0] / $apPeicesArray[1], 2, PHP_ROUND_HALF_DOWN) . ' EV';
		}
		else
		{
			$apertureValue = '';
		}

		// *** Format the focal length
		$focalLength = $exifData['FocalLength'];
		$flPeicesArray = explode('/', $focalLength);
		if (count($flPeicesArray) == 2)
		{
			$focalLength = $flPeicesArray[0] / $flPeicesArray[1] . '.0 mm';
		}
		else
		{
			$focalLength = '';
		}

		// *** Format fNumber
		$fNumber = $exifData['FNumber'];
		$fnPeicesArray = explode('/', $fNumber);
		if (count($fnPeicesArray) == 2)
		{
			$fNumber = $fnPeicesArray[0] / $fnPeicesArray[1];
		}
		else
		{
			$fNumber = '';
		}

		// *** Resolve ExposureProgram
		if (isset($exifData['ExposureProgram']))
		{
			$ep = $exifData['ExposureProgram'];
		}
		if (isset($ep))
		{
			$ep = $this->resolveExposureProgram($ep);
		}

		// *** Resolve MeteringMode
		$mm = $exifData['MeteringMode'];
		$mm = $this->resolveMeteringMode($mm);

		// *** Resolve Flash
		$flash = $exifData['Flash'];
		$flash = $this->resolveFlash($flash);

		if (isset($exifData['Make']))
		{
			$exifDataArray['make'] = $exifData['Make'];
		}
		else
		{
			$exifDataArray['make'] = '';
		}

		if (isset($exifData['Model']))
		{
			$exifDataArray['model'] = $exifData['Model'];
		}
		else
		{
			$exifDataArray['model'] = '';
		}

		if (isset($exifData['DateTime']))
		{
			$exifDataArray['date'] = $exifData['DateTime'];
		}
		else
		{
			$exifDataArray['date'] = '';
		}

		if (isset($exifData['ExposureTime']))
		{
			$exifDataArray['exposure time'] = $exifData['ExposureTime'] . ' sec.';
		}
		else
		{
			$exifDataArray['exposure time'] = '';
		}

		if ($apertureValue != '')
		{
			$exifDataArray['aperture value'] = $apertureValue;
		}
		else
		{
			$exifDataArray['aperture value'] = '';
		}

		if (isset($exifData['COMPUTED']['ApertureFNumber']))
		{
			$exifDataArray['f-stop'] = $exifData['COMPUTED']['ApertureFNumber'];
		}
		else
		{
			$exifDataArray['f-stop'] = '';
		}

		if (isset($exifData['FNumber']))
		{
			$exifDataArray['fnumber'] = $exifData['FNumber'];
		}
		else
		{
			$exifDataArray['fnumber'] = '';
		}

		if ($fNumber != '')
		{
			$exifDataArray['fnumber value'] = $fNumber;
		}
		else
		{
			$exifDataArray['fnumber value'] = '';
		}

		if (isset($exifData['ISOSpeedRatings']))
		{
			$exifDataArray['iso'] = $exifData['ISOSpeedRatings'];
		}
		else
		{
			$exifDataArray['iso'] = '';
		}

		if ($focalLength != '')
		{
			$exifDataArray['focal length'] = $focalLength;
		}
		else
		{
			$exifDataArray['focal length'] = '';
		}

		if (isset($ep))
		{
			$exifDataArray['exposure program'] = $ep;
		}
		else
		{
			$exifDataArray['exposure program'] = '';
		}

		if ($mm != '')
		{
			$exifDataArray['metering mode'] = $mm;
		}
		else
		{
			$exifDataArray['metering mode'] = '';
		}

		if ($flash != '')
		{
			$exifDataArray['flash status'] = $flash;
		}
		else
		{
			$exifDataArray['flash status'] = '';
		}

		if (isset($exifData['Artist']))
		{
			$exifDataArray['creator'] = $exifData['Artist'];
		}
		else
		{
			$exifDataArray['creator'] = '';
		}

		if (isset($exifData['Copyright']))
		{
			$exifDataArray['copyright'] = $exifData['Copyright'];
		}
		else
		{
			$exifDataArray['copyright'] = '';
		}

		// *** Orientation
		if (isset($exifData['Orientation']))
		{
			$exifDataArray['orientation'] = $exifData['Orientation'];
		}
		else
		{
			$exifDataArray['orientation'] = '';
		}

		return $exifDataArray;
	}

	private function resolveExposureProgram($ep)
	{
		switch ($ep)
		{
			case 0:
				$ep = '';
				break;
			case 1:
				$ep = 'manual';
				break;
			case 2:
				$ep = 'normal program';
				break;
			case 3:
				$ep = 'aperture priority';
				break;
			case 4:
				$ep = 'shutter priority';
				break;
			case 5:
				$ep = 'creative program';
				break;
			case 6:
				$ep = 'action program';
				break;
			case 7:
				$ep = 'portrait mode';
				break;
			case 8:
				$ep = 'landscape mode';
				break;

			default:
				break;
		}

		return $ep;
	}

	private function openImage($file)
	{
		if ( ! file_exists($file) && ! $this->checkStringStartsWith('http://', $file))
		{
			if ($this->debug)
			{
				throw new Exception('Image not found.');
			}
			else
			{
				throw new Exception();
			}
		};

		// *** Get extension
		$extension = strrchr($file, '.');
		$extension = fix_strtolower($extension);
		switch ($extension)
		{
			case '.jpg':
			case '.jpeg':
				$img = @imagecreatefromjpeg($file);
				break;
			case '.gif':
				$img = @imagecreatefromgif($file);
				break;
			case '.png':
				$img = @imagecreatefrompng($file);
				break;
			case '.bmp':
				$img = @$this->imagecreatefrombmp($file);
				break;
			case '.psd':
				$img = @$this->imagecreatefrompsd($file);
				break;

			// ... etc

			default:
				$img = false;
				break;
		}

		return $img;
	}

	public function saveImage($savePath, $imageQuality = "100")
    # Author:     Jarrod Oberto
    # Date:       27-02-08
    # Purpose:    Saves the image
    # Param in:   $savePath: Where to save the image including filename:
    #             $imageQuality: image quality you want the image saved at 0-100
    # Param out:  n/a
    # Reference:
    # Notes:    * gif doesn't have a quality parameter
    #       * jpg has a quality setting 0-100 (100 being the best)
    #       * png has a quality setting 0-9 (0 being the best)
    #
    #             * bmp files have no native support for bmp files. We use a
    #       third party class to save as bmp.
	{

		// *** Perform a check or two.
		if ( ! is_resource($this->imageResized))
		{
			if ($this->debug)
			{
				throw new Exception('saveImage: This is not a resource.');
			}
			else
			{
				throw new Exception();
			}
		}
		$fileInfoArray = pathInfo($savePath);
		clearstatcache();
		if ( ! is_writable($fileInfoArray['dirname']))
		{
			if ($this->debug)
			{
				throw new Exception('The path is not writable. Please check your permissions.');
			}
			else
			{
				throw new Exception();
			}
		}

		// *** Get extension
		$extension = strrchr($savePath, '.');
		$extension = fix_strtolower($extension);

		$error = '';

		switch ($extension)
		{
			case '.jpg':
			case '.jpeg':
				if (imagetypes() & IMG_JPG)
				{
					imagejpeg($this->imageResized, $savePath, $imageQuality);
				}
				else
				{
					$error = 'jpg';
				}
				break;

			case '.gif':
				if (imagetypes() & IMG_GIF)
				{
					imagegif($this->imageResized, $savePath);
				}
				else
				{
					$error = 'gif';
				}
				break;

			case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality / 100) * 9);

				// *** Invert qualit setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;

				if (imagetypes() & IMG_PNG)
				{
					imagepng($this->imageResized, $savePath, $invertScaleQuality);
				}
				else
				{
					$error = 'png';
				}
				break;

			case '.bmp':
				file_put_contents($savePath, $this->GD2BMPstring($this->imageResized));
				break;

			// ... etc

			default:
				// *** No extension - No save.
				$this->errorArray[] = 'This file type (' . $extension . ') is not supported. File not saved.';
				break;
		}

		//imagedestroy($this->imageResized);

		// *** Display error if a file type is not supported.
		if ($error != '')
		{
			$this->errorArray[] = $error . ' support is NOT enabled. File not saved.';
		}
	}

	public function getFileName()
	{
		return $this->fileName;
	}

	public function testGDInstalled()
	{
		if (extension_loaded('gd') && function_exists('gd_info'))
		{
			$gdInstalled = true;
		}
		else
		{
			$gdInstalled = false;
		}

		return $gdInstalled;
	}

	public function testEXIFInstalled()
	{
		if (extension_loaded('exif'))
		{
			$exifInstalled = true;
		}
		else
		{
			$exifInstalled = false;
		}

		return $exifInstalled;
	}

	public function testIsImage($image)
	{
		if ($image)
		{
			$fileIsImage = true;
		}
		else
		{
			$fileIsImage = false;
		}

		return $fileIsImage;
	}

	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos)
    # Author:     Jarrod Oberto
    # Date:       15-09-08
    # Purpose:    Crops the image
    # Param in:   $newWidth:
    #             $newHeight:
    # Param out:  n/a
    # Reference:
    # Notes:
    #
	{

		// *** Get cropping co-ordinates
		$cropArray = $this->getCropPlacing($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos);
		$cropStartX = $cropArray['x'];
		$cropStartY = $cropArray['y'];

		// *** Crop this bad boy
		$crop = imagecreatetruecolor($newWidth, $newHeight);
		$this->keepTransparancy($optimalWidth, $optimalHeight, $crop);
		imagecopyresampled($crop, $this->imageResized, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight);

		$this->imageResized = $crop;

		// *** Set new width and height to our variables
		$this->width = $newWidth;
		$this->height = $newHeight;

	}

	private function getCropPlacing($optimalWidth, $optimalHeight, $newWidth, $newHeight, $pos = 'm')
    #
    # Author:   Jarrod Oberto
    # Date:   July 11
    # Purpose:  Set the cropping area.
    # Params in:
    # Params out: (array) the crop x and y co-ordinates.
    # Notes:    When specifying the exact pixel crop position (eg 10x15), be
    #       very careful as it's easy to crop out of the image leaving
    #       black borders.
    #
	{
		$pos = fix_strtolower($pos);

		// *** If co-ords have been entered
		if (strstr($pos, 'x'))
		{
			$pos = str_replace(' ', '', $pos);

			$xyArray = explode('x', $pos);
			list($cropStartX, $cropStartY) = $xyArray;

		}
		else
		{

			switch ($pos)
			{
				case 'tl':
					$cropStartX = 0;
					$cropStartY = 0;
					break;

				case 't':
					$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
					$cropStartY = 0;
					break;

				case 'tr':
					$cropStartX = $optimalWidth - $newWidth;
					$cropStartY = 0;
					break;

				case 'l':
					$cropStartX = 0;
					$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
					break;

				case 'm':
					$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
					$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
					break;

				case 'r':
					$cropStartX = $optimalWidth - $newWidth;
					$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
					break;

				case 'bl':
					$cropStartX = 0;
					$cropStartY = $optimalHeight - $newHeight;
					break;

				case 'b':
					$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
					$cropStartY = $optimalHeight - $newHeight;
					break;

				case 'br':
					$cropStartX = $optimalWidth - $newWidth;
					$cropStartY = $optimalHeight - $newHeight;
					break;

				case 'auto':
					// *** If image is a portrait crop from top, not center. v1.5
					if ($optimalHeight > $optimalWidth)
					{
						$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
						$cropStartY = ($this->cropFromTopPercent / 100) * $optimalHeight;
					}
					else
					{

						// *** Else crop from the center
						$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
						$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
					}
					break;

				default:
					// *** Default to center
					$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
					$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
					break;
			}
		}

		return array( 'x' => $cropStartX, 'y' => $cropStartY );
	}

	private function getDimensions($newWidth, $newHeight, $option)
	{

		switch (strval($option))
		{
			case '0':
			case 'exact':
				$optimalWidth = $newWidth;
				$optimalHeight = $newHeight;
				break;
			case '1':
			case 'portrait':
				$dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
				break;
			case '2':
			case 'landscape':
				$dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
				break;
			case '3':
			case 'auto':
				$dimensionsArray = $this->getSizeByAuto($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
				break;
			case '4':
			case 'crop':
				$dimensionsArray = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
				break;
		}

		return array( 'optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight );
	}

	private function getSizeByFixedHeight($newWidth, $newHeight)
	{
		// *** If forcing is off...
		if ( ! $this->forceStretch)
		{

			// *** ...check if actual height is less than target height
			if ($this->height < $newHeight)
			{
				return array( 'optimalWidth' => $this->width, 'optimalHeight' => $this->height );
			}
		}

		$ratio = $this->width / $this->height;

		$newWidth = $newHeight * $ratio;

		//return $newWidth;
		return array( 'optimalWidth' => $newWidth, 'optimalHeight' => $newHeight );
	}

	private function getSizeByFixedWidth($newWidth, $newHeight)
	{
		// *** If forcing is off...
		if ( ! $this->forceStretch)
		{

			// *** ...check if actual width is less than target width
			if ($this->width < $newWidth)
			{
				return array( 'optimalWidth' => $this->width, 'optimalHeight' => $this->height );
			}
		}

		$ratio = $this->height / $this->width;

		$newHeight = $newWidth * $ratio;

		//return $newHeight;
		return array( 'optimalWidth' => $newWidth, 'optimalHeight' => $newHeight );
	}

	private function getSizeByAuto($newWidth, $newHeight)
    # Author:     Jarrod Oberto
    # Date:       19-08-08
    # Purpose:    Depending on the height, choose to resize by 0, 1, or 2
    # Param in:   The new height and new width
    # Notes:
    #
	{
		// *** If forcing is off...
		if ( ! $this->forceStretch)
		{

			// *** ...check if actual size is less than target size
			if ($this->width < $newWidth && $this->height < $newHeight)
			{
				return array( 'optimalWidth' => $this->width, 'optimalHeight' => $this->height );
			}
		}

		if ($this->height < $this->width)
        // *** Image to be resized is wider (landscape)
		{
			//$optimalWidth = $newWidth;
			//$optimalHeight= $this->getSizeByFixedWidth($newWidth);

			$dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
			$optimalWidth = $dimensionsArray['optimalWidth'];
			$optimalHeight = $dimensionsArray['optimalHeight'];
		}
		elseif ($this->height > $this->width)
        // *** Image to be resized is taller (portrait)
		{
			//$optimalWidth = $this->getSizeByFixedHeight($newHeight);
			//$optimalHeight= $newHeight;

			$dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
			$optimalWidth = $dimensionsArray['optimalWidth'];
			$optimalHeight = $dimensionsArray['optimalHeight'];
		}
		else
        // *** Image to be resizerd is a square
		{

			if ($newHeight < $newWidth)
			{
				//$optimalWidth = $newWidth;
				//$optimalHeight= $this->getSizeByFixedWidth($newWidth);
				$dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
			}
			else
			{
				if ($newHeight > $newWidth)
				{
					//$optimalWidth = $this->getSizeByFixedHeight($newHeight);
					//$optimalHeight= $newHeight;
					$dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
					$optimalWidth = $dimensionsArray['optimalWidth'];
					$optimalHeight = $dimensionsArray['optimalHeight'];
				}
				else
				{
					// *** Sqaure being resized to a square
					$optimalWidth = $newWidth;
					$optimalHeight = $newHeight;
				}
			}
		}

		return array( 'optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight );
	}

	private function getOptimalCrop($newWidth, $newHeight)
    # Author:     Jarrod Oberto
    # Date:       17-11-09
    # Purpose:    Get optimal crop dimensions
    # Param in:   width and height as requested by user (fig 3)
    # Param out:  Array of optimal width and height (fig 2)
    # Reference:
    # Notes:      The optimal width and height return are not the same as the
    #       same as the width and height passed in. For example:
    #
    #
    #   |-----------------|     |------------|       |-------|
    #   |             |   =>  |**|      |**|   =>  |       |
    #   |             |     |**|      |**|       |       |
    #   |           |       |------------|       |-------|
    #   |-----------------|
    #        original                optimal             crop
    #              size                   size               size
    #  Fig          1                      2                  3
    #
    #       300 x 250           150 x 125          150 x 100
    #
    #    The optimal size is the smallest size (that is closest to the crop size)
    #    while retaining proportion/ratio.
    #
    #  The crop size is the optimal size that has been cropped on one axis to
    #  make the image the exact size specified by the user.
    #
    #               * represent cropped area
    #
	{

		// *** If forcing is off...
		if ( ! $this->forceStretch)
		{

			// *** ...check if actual size is less than target size
			if ($this->width < $newWidth && $this->height < $newHeight)
			{
				return array( 'optimalWidth' => $this->width, 'optimalHeight' => $this->height );
			}
		}

		$heightRatio = $this->height / $newHeight;
		$widthRatio = $this->width / $newWidth;

		if ($heightRatio < $widthRatio)
		{
			$optimalRatio = $heightRatio;
		}
		else
		{
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = round($this->height / $optimalRatio);
		$optimalWidth = round($this->width / $optimalRatio);

		return array( 'optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight );
	}

	private function resolveMeteringMode($mm)
	{
		switch ($mm)
		{
			case 0:
				$mm = 'unknown';
				break;
			case 1:
				$mm = 'average';
				break;
			case 2:
				$mm = 'center weighted average';
				break;
			case 3:
				$mm = 'spot';
				break;
			case 4:
				$mm = 'multi spot';
				break;
			case 5:
				$mm = 'pattern';
				break;
			case 6:
				$mm = 'partial';
				break;
			case 255:
				$mm = 'other';
				break;

			default:
				break;
		}

		return $mm;
	}

	private function resolveFlash($flash)
	{
		switch ($flash)
		{
			case 0:
				$flash = 'flash did not fire';
				break;
			case 1:
				$flash = 'flash fired';
				break;
			case 5:
				$flash = 'strobe return light not detected';
				break;
			case 7:
				$flash = 'strobe return light detected';
				break;
			case 9:
				$flash = 'flash fired, compulsory flash mode';
				break;
			case 13:
				$flash = 'flash fired, compulsory flash mode, return light not detected';
				break;
			case 15:
				$flash = 'flash fired, compulsory flash mode, return light detected';
				break;
			case 16:
				$flash = 'flash did not fire, compulsory flash mode';
				break;
			case 24:
				$flash = 'flash did not fire, auto mode';
				break;
			case 25:
				$flash = 'flash fired, auto mode';
				break;
			case 29:
				$flash = 'flash fired, auto mode, return light not detected';
				break;
			case 31:
				$flash = 'flash fired, auto mode, return light detected';
				break;
			case 32:
				$flash = 'no flash function';
				break;
			case 65:
				$flash = 'flash fired, red-eye reduction mode';
				break;
			case 69:
				$flash = 'flash fired, red-eye reduction mode, return light not detected';
				break;
			case 71:
				$flash = 'flash fired, red-eye reduction mode, return light detected';
				break;
			case 73:
				$flash = 'flash fired, compulsory flash mode, red-eye reduction mode';
				break;
			case 77:
				$flash = 'flash fired, compulsory flash mode, red-eye reduction mode, return light not detected';
				break;
			case 79:
				$flash = 'flash fired, compulsory flash mode, red-eye reduction mode, return light detected';
				break;
			case 89:
				$flash = 'flash fired, auto mode, red-eye reduction mode';
				break;
			case 93:
				$flash = 'flash fired, auto mode, return light not detected, red-eye reduction mode';
				break;
			case 95:
				$flash = 'flash fired, auto mode, return light detected, red-eye reduction mode';
				break;

			default:
				break;
		}

		return $flash;

	}

	function checkStringStartsWith($needle, $haystack)
    # Check if a string starts with a specific pattern
	{
		return (substr($haystack, 0, strlen($needle)) == $needle);
	}

	private function GD2BMPstring(&$gd_image)
    # Author:     James Heinrich
    # Purpose:    Save file as type bmp
    # Param in:   The image canvas (passed as ref)
    # Param out:
    # Reference:
    # Notes:    This code was stripped out of two external files
    #       (phpthumb.bmp.php,phpthumb.functions.php) and added below to
    #       avoid dependancies.
    #
	{
		$imageX = ImageSX($gd_image);
		$imageY = ImageSY($gd_image);

		$BMP = '';
		for ($y = ($imageY - 1); $y >= 0; $y--)
		{
			$thisline = '';
			for ($x = 0; $x < $imageX; $x++)
			{
				$argb = $this->GetPixelColor($gd_image, $x, $y);
				$thisline .= chr($argb['blue']) . chr($argb['green']) . chr($argb['red']);
			}
			while (strlen($thisline) % 4)
			{
				$thisline .= "\x00";
			}
			$BMP .= $thisline;
		}

		$bmpSize = strlen($BMP) + 14 + 40;
		// BITMAPFILEHEADER [14 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_62uq.asp
		$BITMAPFILEHEADER = 'BM';                                    // WORD    bfType;
		$BITMAPFILEHEADER .= $this->LittleEndian2String($bmpSize, 4); // DWORD   bfSize;
		$BITMAPFILEHEADER .= $this->LittleEndian2String(0, 2); // WORD    bfReserved1;
		$BITMAPFILEHEADER .= $this->LittleEndian2String(0, 2); // WORD    bfReserved2;
		$BITMAPFILEHEADER .= $this->LittleEndian2String(54, 4); // DWORD   bfOffBits;

		// BITMAPINFOHEADER - [40 bytes] http://msdn.microsoft.com/library/en-us/gdi/bitmaps_1rw2.asp
		$BITMAPINFOHEADER = $this->LittleEndian2String(40, 4); // DWORD  biSize;
		$BITMAPINFOHEADER .= $this->LittleEndian2String($imageX, 4); // LONG   biWidth;
		$BITMAPINFOHEADER .= $this->LittleEndian2String($imageY, 4); // LONG   biHeight;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(1, 2); // WORD   biPlanes;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(24, 2); // WORD   biBitCount;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biCompression;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biSizeImage;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(2835, 4); // LONG   biXPelsPerMeter;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(2835, 4); // LONG   biYPelsPerMeter;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biClrUsed;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biClrImportant;

		return $BITMAPFILEHEADER . $BITMAPINFOHEADER . $BMP;
	}

	private function GetPixelColor(&$img, $x, $y)
    # Author:     James Heinrich
    # Purpose:
    # Param in:
    # Param out:
    # Reference:
    # Notes:
    #
	{
		if ( ! is_resource($img))
		{
			return false;
		}

		return @ImageColorsForIndex($img, @ImageColorAt($img, $x, $y));
	}

	private function LittleEndian2String($number, $minbytes = 1)
    # Author:     James Heinrich
    # Purpose:    BMP SUPPORT (SAVING)
    # Param in:
    # Param out:
    # Reference:
    # Notes:
    #
	{
		$intstring = '';
		while ($number > 0)
		{
			$intstring = $intstring . chr($number & 255);
			$number >>= 8;
		}

		return str_pad($intstring, $minbytes, "\x00", STR_PAD_RIGHT);
	}

	private function ImageCreateFromBMP($filename)
	{
		//Ouverture du fichier en mode binaire
		if ( ! $f1 = fopen($filename, "rb"))
		{
			return false;
		}

		//1 : Chargement des ent�tes FICHIER
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
		if ($FILE['file_type'] != 19778)
		{
			return false;
		}

		//2 : Chargement des ent�tes BMP
		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
			'/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
			'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
		$BMP['colors'] = pow(2, $BMP['bits_per_pixel']);

		if ($BMP['size_bitmap'] == 0)
		{
			$BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		}

		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
		$BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
		$BMP['decal'] = 4 - (4 * $BMP['decal']);

		if ($BMP['decal'] == 4)
		{
			$BMP['decal'] = 0;
		}

		//3 : Chargement des couleurs de la palette
		$PALETTE = array();
		if ($BMP['colors'] < 16777216)
		{
			$PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
		}

		//4 : Cr�ation de l'image
		$IMG = fread($f1, $BMP['size_bitmap']);
		$VIDE = chr(0);

		$res = imagecreatetruecolor($BMP['width'], $BMP['height']);
		$P = 0;
		$Y = $BMP['height'] - 1;
		while ($Y >= 0)
		{
			$X = 0;
			while ($X < $BMP['width'])
			{
				if ($BMP['bits_per_pixel'] == 24)
				{
					$COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
				}
				elseif ($BMP['bits_per_pixel'] == 16)
				{

					/*
                     * BMP 16bit fix
                     * =================
                     *
                     * Ref: http://us3.php.net/manual/en/function.imagecreate.php#81604
                     *
                     * Notes:
                     * "don't work with bmp 16 bits_per_pixel. change pixel
                     * generator for this."
                     *
                     */

					// *** Original code (don't work)
					//$COLOR = unpack("n",substr($IMG,$P,2));
					//$COLOR[1] = $PALETTE[$COLOR[1]+1];

					$COLOR = unpack("v", substr($IMG, $P, 2));
					$blue = ($COLOR[1] & 0x001f) << 3;
					$green = ($COLOR[1] & 0x07e0) >> 3;
					$red = ($COLOR[1] & 0xf800) >> 8;
					$COLOR[1] = $red * 65536 + $green * 256 + $blue;

				}
				elseif ($BMP['bits_per_pixel'] == 8)
				{
					$COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
					$COLOR[1] = $PALETTE[ $COLOR[1] + 1 ];
				}
				elseif ($BMP['bits_per_pixel'] == 4)
				{
					$COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if (($P * 2) % 2 == 0)
					{
						$COLOR[1] = ($COLOR[1] >> 4);
					}
					else
					{
						$COLOR[1] = ($COLOR[1] & 0x0F);
					}
					$COLOR[1] = $PALETTE[ $COLOR[1] + 1 ];
				}
				elseif ($BMP['bits_per_pixel'] == 1)
				{
					$COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if (($P * 8) % 8 == 0)
					{
						$COLOR[1] = $COLOR[1] >> 7;
					}
					elseif (($P * 8) % 8 == 1)
					{
						$COLOR[1] = ($COLOR[1] & 0x40) >> 6;
					}
					elseif (($P * 8) % 8 == 2)
					{
						$COLOR[1] = ($COLOR[1] & 0x20) >> 5;
					}
					elseif (($P * 8) % 8 == 3)
					{
						$COLOR[1] = ($COLOR[1] & 0x10) >> 4;
					}
					elseif (($P * 8) % 8 == 4)
					{
						$COLOR[1] = ($COLOR[1] & 0x8) >> 3;
					}
					elseif (($P * 8) % 8 == 5)
					{
						$COLOR[1] = ($COLOR[1] & 0x4) >> 2;
					}
					elseif (($P * 8) % 8 == 6)
					{
						$COLOR[1] = ($COLOR[1] & 0x2) >> 1;
					}
					elseif (($P * 8) % 8 == 7)
					{
						$COLOR[1] = ($COLOR[1] & 0x1);
					}
					$COLOR[1] = $PALETTE[ $COLOR[1] + 1 ];
				}
				else
				{
					return false;
				}

				imagesetpixel($res, $X, $Y, $COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}

			$Y--;
			$P += $BMP['decal'];
		}
		//Fermeture du fichier
		fclose($f1);

		return $res;
	}

	private function imagecreatefrompsd($fileName)
    # Author:     Tim de Koning
    # Version:    1.3
    # Purpose:    To create an image from a PSD file.
    # Param in:   PSD file to open.
    # Param out:  Return a resource like the other ImageCreateFrom functions
    # Reference:  http://www.kingsquare.nl/phppsdreader
    # Notes:
    #
	{
		if (file_exists($this->psdReaderPath))
		{

			include_once($this->psdReaderPath);

			$psdReader = new PhpPsdReader($fileName);

			if (isset($psdReader->infoArray['error']))
			{
				return '';
			}
			else
			{
				return $psdReader->getImage();
			}
		}
		else
		{
			return false;
		}
	}

	public function __destruct()
	{
		if (is_resource($this->imageResized))
		{
			imagedestroy($this->imageResized);
		}
	}
}
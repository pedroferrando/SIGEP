<?php 

"Copyright (C) 2013 <Pezzarini Pedro Jose (jose2190@gmail.com)>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.";



/**
* 
*/
class ImageRender
{
	var $imageString = "";
	var $savedFile = "";
	var $type = "";
	var $savedFileJpeg = "";

	function __construct()
	{
		
	}

	# Documentacion para metodo "render" con $imageString como parámetros
	public function render($imageString){
		$this->imageString = $imageString;
	}

	# Documentacion para metodo "decode" con $imageString como parámetros
	public function decode(){
		$data = str_replace(' ','+',$this->imageString);
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);

		$this->type = $type;
		
		return(base64_decode($data));
	}

	# Documentacion para metodo "saveFile" con $filePath, $fileName, $extension = "" como parámetros
	public function saveFile($filePath, $fileName = "", $extension = ""){

		if (strlen($fileName) < 1) {
			$fileName = uniqid();
		}

		if (strlen($extension) < 1) {
			list($type, $data) = explode('/', $this->type);
			$extension = $type;
		}
		

		$pathFile = $filePath . "/" . $fileName . "." . $extension;
		$fileBin = fopen($pathFile, "wb");

		$imageData = $this->decode();

		fwrite($fileBin, $imageData);
		fclose($fileBin);
		$this->savedFile = $pathFile;
		return($this->savedFile);
	}

	# Documentacion para metodo "saveToJpeg" con $originalFile, $outputFile, $quality como parámetros
	public function saveToJpeg($quality) {
		if ((strlen($this->imageString) > 0) and (strlen($this->savedFile) > 0)) {
			$originalFile = $this->savedFile;
			$outputFile = $this->savedFile . "-converted" . ".jpeg";

			$image = imagecreatefrompng($originalFile);
			imagejpeg($image, $outputFile, $quality);
			imagedestroy($image);

			$this->savedFileJpeg = $outputFile;
		} else {
			echo "error";
		}
	}


}

?>
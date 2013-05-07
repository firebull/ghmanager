<?php
class Map extends AppModel {
	public $name = 'Map';
	public $displayField = 'name';


	public $belongsTo = array(
		'MapType' => array(
			'className' => 'MapType',
			'foreignKey' => 'map_type_id',
			'conditions' => '',
			'fields' => 'id, name, longname',
			'order' => ''
		)
	);

	public $hasAndBelongsToMany = array(
		'GameTemplate' => array(
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_maps',
			'foreignKey' => 'map_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id, name',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);


		// Вывод ошщибок загрузки файлов
	function fileUploadErrorMessage( $error_code = null) {
	    switch ($error_code) {
	        case UPLOAD_ERR_INI_SIZE:
	            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
	        case UPLOAD_ERR_FORM_SIZE:
	            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
	        case UPLOAD_ERR_PARTIAL:
	            return 'The uploaded file was only partially uploaded';
	        case UPLOAD_ERR_NO_FILE:
	            return 'No file was uploaded';
	        case UPLOAD_ERR_NO_TMP_DIR:
	            return 'Missing a temporary folder';
	        case UPLOAD_ERR_CANT_WRITE:
	            return 'Failed to write file to disk';
	        case UPLOAD_ERR_EXTENSION:
	            return 'File upload stopped by extension';
	        default:
	            return 'Unknown upload error';
	    }
	}
	// Проверка загруженного файла на соответсвие правильным расширениям
	function imageIsAllowedType ( $type = null ) {
		$allowedType = array (	//'image/gif',
								'image/jpeg',
								//'image/png'
								);
		return in_array ( $type, $allowedType );
	}

	//Преобразование типа файла в его расширение
	function typeToExtension ($type = null){
		$extension = array (	'image/gif'  => 'gif',
								'image/jpeg' => 'jpg',
								'image/png'  => 'png'

							);
		return $extension[$type];
	}

	// проверка размера изображения - вес, ширина-высота
	function imageIsAllowedSize ($image = null){
		$height = 1280; // pixels
		$width  = 1024; // pixels
		$size   = 500000;  // bytes
		list($image_width, $image_height) = getimagesize($image['tmp_name']);
		$error_message = '';
		$error = false;

		if ($image['size'] > $size){
			$error_message = 'Размер изображения больше разрешенных '.$size.'Kb<br/>';
			$error = true;
		}

		if ($image_height != $height){
			$error_message .= 'Высота изображения должна быть '.$height.' точек, а не '.$image_height.' <br/>';
			$error = true;
		}

		if ($image_width > $width){
			$error_message .= 'Ширина изображения должна быть '.$width.' точек, а не '.$image_width;
			$error = true;
		}

		if ($error === true){
		 	return $error_message;
		}
		else
		{
			return true;
		}

	}

	function resizeImage ($image = null, $saveName = null, $path = "/img/gameMaps/") {
			$maxWidth  = 320;
			$maxHeight = 240;

			$thumbWidth  = 100;
			$thumbHeight = 75;

			$path = WWW_ROOT.$path;
			$logo = WWW_ROOT.'/img/ghmanager_272x42.png';
			// Get sizes
			list($width, $height) = getimagesize($image);

			// Размеры лого
			list($logoWidth, $logoHeight) = getimagesize($logo);

			$newLogoWidth = 85;
			$newLogoHeight = intval(($newLogoWidth/$logoWidth)*$logoHeight);

			// Load
			$resizedImage = imagecreatetruecolor($maxWidth, $maxHeight);
			$thumb  = imagecreatetruecolor($thumbWidth, $thumbHeight);
			$source = imagecreatefromjpeg($image);
			$logoSource = imagecreatefrompng($logo);

			// Resize
			 if (imagecopyresized($resizedImage, $source, 0, 0, 0, 0, $maxWidth, $maxHeight, $width, $height)
			 	  and
			 	 imagecopyresized($resizedImage, $logoSource, 235, 240-5-$newLogoHeight, 0, 0, $newLogoWidth, $newLogoHeight, $logoWidth, $logoHeight)
			 	  and
			 	 imagecopyresized($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height)){
			 		// Output
			 		imagefilter($resizedImage, IMG_FILTER_SMOOTH, 35);
					imagefilter($thumb, IMG_FILTER_SMOOTH, 20);
			 		if ( imagejpeg($resizedImage, $path.$saveName.'.jpg', 90)
			 				and
						 imagejpeg($thumb, $path.$saveName.'_thumb.jpg', 95))
					   {

					 		chmod ($resizedImage, 0664);
					 		chmod ($thumb, 0664);
					 		return true;
					   }
					   else
					   {
					   		return false;
					   }
			 }
			 else
			 {
			 	return false;
			 }





	}

}
?>
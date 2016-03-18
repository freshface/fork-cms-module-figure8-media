<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Upload image action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */

class UploadImage extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		$verifyToken =  md5(\SpoonFilter::getPostValue('timestamp', null, '', 'string'));
		$token = \SpoonFilter::getPostValue('token', null, '', 'string');
		$this->id = \SpoonFilter::getPostValue('album_id', null, '', 'int');


		if(!empty($_FILES))
		{
			if ($token == $verifyToken)
			{	
				ini_set('memory_limit', -1);

				$this->record = (array) BackendMediaModel::getAlbum($this->id);

				if(empty($this->record)) $this->output(self::ERROR, null, 'album not found');

				$this->set_id = $this->record['set_id'];

				// There is no set linked
				if($this->set_id === null)
				{
					// Create a set based on the album name
					$item['title'] = $this->record['title'];
					$item['language'] = BL::getWorkingLanguage();
					$item['created_on'] = BackendModel::getUTCDate();
					$item['edited_on'] = BackendModel::getUTCDate();

					// Create set AND set the set_id
					$this->set_id = BackendMediaModel::insertSet($item);

					// Link set to album
					BackendMediaModel::updateAlbum(array('id' => $this->id, 'set_id' => $this->set_id));
				}

				// Upload
				self::uploadImage($_FILES, $this->set_id);

				// Update some statistics
				BackendMediaModel::updateSetStatistics($this->set_id);

				ini_restore('memory_limit');

				$this->output(self::OK, null, '1');
			}
			else
			{
				$this->output(self::ERROR, null, 'invalid token');
			}
		}
		else
		{
			$this->output(self::ERROR, null, 'no files selected');
		}
	}


	private function isImage($tempFile) {

		// Get the size of the image
	    $size = getimagesize($tempFile);

		if (isset($size) && $size[0] && $size[1] && $size[0] *  $size[1] > 0) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Validate the image
	 *
	 * @param string $field The name of the field
	 * @param int $set_idThe id of the set
	 */
	private function uploadImage($file, $set_id)
	{
		// image provided
		$fileData = $file['Filedata'];

		if($fileData)
		{
			$fileParts = pathinfo($fileData['name']);
			$tempFile   = $fileData['tmp_name'];

			$extension = $fileParts['extension'];
			$original_filename = $fileParts['filename'];

			$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions


			if (in_array(strtolower($fileParts['extension']), $fileTypes) && filesize($tempFile) > 0 && self::isImage($tempFile))
			{

				// Get languages where set is linked to
				$linkedAlbums = BackendMediaModel::getAlbumsLinkedToSet($this->set_id);

				// Generate a unique filename
				$filename = BackendMediaModel::getFilenameForImage(time() . '_' . $original_filename, $extension) . '.' . $extension;

				$image['filename'] = $filename;
				$image['set_id'] = $set_id;
				$image['original_filename'] = $original_filename;
				$image['hidden'] = 'N';
				$image['created_on'] = BackendModel::getUTCDate();
				$image['edited_on'] = BackendModel::getUTCDate();
				$image['sequence'] = BackendMediaModel::getSetImageSequence($set_id) + 1;

				$content = array();
				$metaData = array();

				foreach($linkedAlbums as &$linkedAlbum)
				{
					// Meta
					$meta['keywords'] = $original_filename;
					$meta['keywords_overwrite'] = 'N';
					$meta['description'] = $original_filename;
					$meta['description_overwrite'] = 'N';
					$meta['title'] = $original_filename;
					$meta['title_overwrite'] = 'N';
					$meta['url'] = BackendMediaModel::getURLForImage($original_filename, $linkedAlbum['language']);

					// add
					$metaData[$linkedAlbum['language']] = $meta;

					// build record
					$temp = array();
					$temp['title'] = $original_filename;
					$temp['album_id'] = $linkedAlbum['id'];
					$temp['language'] = $linkedAlbum['language'];
					$temp['set_id'] = $set_id;
					$temp['created_on'] = BackendModel::getUTCDate();
					$temp['edited_on'] = BackendModel::getUTCDate();

					// add
					$content[$linkedAlbum['language']] = $temp;
				}

				// Path to the sets folder
				$setsFilesPath = FRONTEND_FILES_PATH . '/Media/sets';

				// Backend resolutions
				foreach(BackendMediaModel::$backendResolutions as $resolution)
				{
					$forceOriginalAspectRatio = $resolution['method'] == 'crop' ? false : true;
					$allowEnlargement = true;


					$thumbnail = new \SpoonThumbnail($tempFile , $resolution['width'], $resolution['height'], true);
					$thumbnail->setAllowEnlargement($allowEnlargement);
					$thumbnail->setForceOriginalAspectRatio($forceOriginalAspectRatio);
					$thumbnail->parseToFile($setsFilesPath . '/backend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $filename, BackendMediaModel::IMAGE_QUALITY);
				}

				$image['id'] = BackendMediaModel::insertImage($image, $content, $metaData);
				
				// Do we need to resize the original image or not?
				if(BackendMediaModel::RESIZE_ORIGINAL_IMAGE)
				{

					// Original, but resize if larger then MAX_ORIGINAL_IMAGE_WIDTH OR MAX_ORIGINAL_IMAGE_HEIGHT
					
					$thumbnail = new \SpoonThumbnail($tempFile , BackendMediaModel::MAX_ORIGINAL_IMAGE_WIDTH, BackendMediaModel::MAX_ORIGINAL_IMAGE_HEIGHT, true);
					$thumbnail->setAllowEnlargement(false);
					$thumbnail->setForceOriginalAspectRatio(true);
					$thumbnail->parseToFile($setsFilesPath . '/original/' . $set_id . '/' . $filename, 100);
					chmod($setsFilesPath . '/original/' . $set_id . '/' . $filename, 0775);
				}
				else
				{
					$fs = new Filesystem();
					$fs->mkdir($setsFilesPath . '/original/' . $set_id, 0775);

					// Move the original image
					move_uploaded_file($tempFile, $setsFilesPath . '/original/' . $set_id . '/' . $filename);
					chmod($setsFilesPath . '/original/' . $set_id . '/' . $filename, 0775);
				}
			}
			else
			{
				$this->output(self::OK, null, 'Invalid file type.');
			}
		}
	}
}

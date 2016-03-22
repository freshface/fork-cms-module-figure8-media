<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

use Common\Uri as CommonUri;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Upload action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */

class Upload extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		$verify_token =  md5(\SpoonFilter::getPostValue('timestamp', null, '', 'string'));
		$token = \SpoonFilter::getPostValue('token', null, '', 'string');
		$this->folder_id = \SpoonFilter::getPostValue('folder_id', null, '', 'int');

		if(!empty($_FILES))
		{
			if ($token == $verify_token)
			{	
				ini_set('memory_limit', -1);
			
				// Upload
				self::upload($_FILES);

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

	/**
	 * Validate the image
	 *
	 * @param string $field The name of the field
	 * @param int $set_idThe id of the set
	 */
	private function upload($file)
	{
		$file_data = $file['Filedata'];

		if($file_data)
		{
			$file_parts = pathinfo($file_data['name']);
			$temp_file   = $file_data['tmp_name'];

			$extension = $file_parts['extension'];
			$original_filename = $file_parts['filename'];

			$allowed_types = BackendMediaModel::getAllAllowedFileMimetypes(); // Allowed file extensions

			if (in_array($file_data['type'], $allowed_types) && filesize($temp_file) > 0)
			{
				// Generate a unique filename
				$filename = BackendMediaModel::getFilename(CommonUri::getUrl($file_parts['filename']) . '.' . $extension);

				// path to folder
				$files_path = FRONTEND_FILES_PATH . '/Media/files';
				$preview_files_path = FRONTEND_FILES_PATH . '/Media/preview_files';
				
				$fs = new Filesystem();
				$fs->mkdir($files_path, 0775);
				$fs->mkdir($preview_files_path, 0775);

				// Move the file
				move_uploaded_file($temp_file, $files_path . '/' . $filename);
				chmod($files_path . '/' . $filename, 0775);

				$insert['filename'] = $filename;
				$insert['original_filename'] = $filename;
				$insert['folder_id'] = $this->folder_id;
				$insert['created_on'] = BackendModel::getUTCDate();
				$insert['edited_on'] = BackendModel::getUTCDate();
				$insert['modified'] = 'N';
				$insert['type'] = BackendMediaModel::getAllAllowedTypeByMimetype($file_data['type']);
				$insert['extension'] = $extension;

				if($insert['type'] == 'image'){

					$thumbnail = new \SpoonThumbnail($files_path . '/' . $filename , 400, 400, true);
					$thumbnail->setAllowEnlargement(true);
					$thumbnail->setForceOriginalAspectRatio(true);
					$thumbnail->parseToFile($preview_files_path . '/' . $filename, 100);


					list($width, $height) = getimagesize($files_path . '/' . $filename);

					$data = array('portrait' => ($width > $height) ? false: true);
					$insert['data'] = serialize($data);
				}

				$insert['id'] = BackendMediaModel::insertFile($insert);
				
			}
			else
			{
				echo 'Invalid file type.';
				exit;
				//$this->output(self::ERROR, null, 'Invalid file type.');
			}
		}
	}
}

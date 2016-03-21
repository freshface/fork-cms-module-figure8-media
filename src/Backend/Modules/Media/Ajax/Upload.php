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

				$insert['filename'] = $filename;
				$insert['original_filename'] = $filename;
				$insert['folder_id'] = $this->folder_id;
				$insert['created_on'] = BackendModel::getUTCDate();
				$insert['edited_on'] = BackendModel::getUTCDate();
				$insert['modified'] = 'N';
				$insert['type'] = BackendMediaModel::getAllAllowedTypeByMimetype($file_data['type']);

				// path to folder
				$setsFilesPath = FRONTEND_FILES_PATH . '/Media/files';

				$insert['id'] = BackendMediaModel::insertFile($insert);
				
				$fs = new Filesystem();
				$fs->mkdir($setsFilesPath, 0775);

				// Move the file
				move_uploaded_file($temp_file, $setsFilesPath . '/' . $filename);
				chmod($setsFilesPath . '/' . $filename, 0775);
				
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

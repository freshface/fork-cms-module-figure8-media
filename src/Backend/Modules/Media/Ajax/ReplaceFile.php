<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Common\Uri as CommonUri;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;
use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;

/**
 * Upload action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */

class ReplaceFile extends BackendBaseAJAXAction
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
        $this->id = \SpoonFilter::getPostValue('id', null, '', 'int');
        $this->languages = BackendMediaModel::getAllLanguages();

        if (!empty($_FILES)) {
            if ($token == $verify_token) {
                ini_set('memory_limit', -1);

                $this->record = BackendMediaModel::getFile($this->id);
            
                // Upload
                self::upload($_FILES);

                ini_restore('memory_limit');

                $this->output(self::OK, null, '1');
            } else {
                $this->output(self::ERROR, null, 'invalid token');
            }
        } else {
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

        if ($file_data) {
            $file_parts = pathinfo($file_data['name']);
            $temp_file   = $file_data['tmp_name'];

            $extension = strtolower($file_parts['extension']);
            $original_filename = $file_parts['filename'];

            $allowed_types = BackendMediaModel::getAllAllowedFileMimetypesByType($this->record['type']); // Allowed file types

            if (in_array($file_data['type'], $allowed_types) && filesize($temp_file) > 0) {
                $files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;
                $preview_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;

                $fs = new Filesystem();
                $fs->mkdir($files_path, 0775);
                $fs->mkdir($preview_files_path, 0775);

                if ($this->record['filename']) {
                    $fs->remove($files_path . '/' . $this->record['filename']);
                }
                if ($this->record['original_filename']) {
                    $fs->remove($files_path . '/' . $this->record['original_filename']);
                }

                if ($this->record['filename']) {
                    $fs->remove($preview_files_path . '/' . $this->record['filename']);
                }

                BackendMediaHelper::removeGeneratedFiles(FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_GENERATED_FILES_FOLDER, $this->record['filename']);


                // Generate a unique filename
                $filename = BackendMediaModel::getFilename(CommonUri::getUrl($file_parts['filename']) . '.' . $extension);


                // Move the file
                move_uploaded_file($temp_file, $files_path . '/' . $filename);
                chmod($files_path . '/' . $filename, 0775);

                $update['id'] = $this->record['id'];
                $update['filename'] = $filename;
                $update['original_filename'] = $filename;
                $update['edited_on'] = BackendModel::getUTCDate();
                $update['modified'] = 'N';
                $update['type'] = BackendMediaModel::getAllAllowedTypeByMimetype($file_data['type']);
                $update['extension'] = $extension;

                if ($update['type'] == 'image') {
                    $thumbnail = new \SpoonThumbnail($files_path . '/' . $filename, 400, 400, true);
                    $thumbnail->setAllowEnlargement(true);
                    $thumbnail->setForceOriginalAspectRatio(true);
                    $thumbnail->parseToFile($preview_files_path . '/' . $filename, 100);

                    list($width, $height) = getimagesize($files_path . '/' . $filename);

                    $this->record['data']['portrait'] = ($width > $height) ? false: true;
                    $update['data'] = serialize($this->record['data']);
                }

                BackendMediaModel::updateFile($update);
            } else {
                echo 'Invalid file type.';
                exit;
                //$this->output(self::ERROR, null, 'Invalid file type.');
            }
        }
    }
}

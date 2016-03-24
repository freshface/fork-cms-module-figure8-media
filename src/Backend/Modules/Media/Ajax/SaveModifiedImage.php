<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class SaveModifiedImage extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent
        parent::execute();

        // get parameters
        $id = \SpoonFilter::getPostValue('id', null, 0, 'int');
        $url = \SpoonFilter::getPostValue('url', null, '', 'string');

        // init validation
        $errors = array();

        $success = false;

        // validate
        if ($id === 0) {
            $errors[] = 'no id provided';
        }

        if ($url === '') {
            $errors[] = 'no url provided';
        }

        // got errors
        if (!empty($errors)) {
            $this->output(self::BAD_REQUEST, array('errors' => $errors), 'not all fields were filled');
        } else {
            $this->record = BackendMediaModel::getFile($id);
            $files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;


            $path_parts_url = pathinfo($url);
            $extension = strtolower($path_parts_url['extension']);
            $path_parts_filename = pathinfo($this->record['filename']);

            $new_filename = 'modified-' . $path_parts_filename['filename'] . '.' . $extension;

            // download
            $downloaded = \SpoonFile::download($url, $files_path . '/' . $new_filename);

            if($downloaded)
            {
                
                $preview_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;
                $preview_files_url = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;

                // remove preview file
                $fs = new Filesystem();
                $fs->remove($preview_files_path . '/' . $this->record['filename']);

                // remove generated files
                BackendMediaHelper::removeGeneratedFiles($this->record['filename']);

                $update = array(
                    'id' => $id,
                    'modified' => 'Y',
                    'extension' => $extension,
                    'filename' => $new_filename
                );


                // generate preview file
                $thumbnail = new \SpoonThumbnail($files_path . '/' . $new_filename , 400, 400, true);
                $thumbnail->setAllowEnlargement(true);
                $thumbnail->setForceOriginalAspectRatio(true);
                $thumbnail->parseToFile($preview_files_path . '/' . $new_filename, 100);

                list($width, $height) = getimagesize($files_path . '/' . $new_filename);

                $data = array('portrait' => ($width > $height) ? false: true);

                $update['data'] = serialize($data);

                $success = BackendMediaModel::updateFile($update);
            }


            // output
            if ($success) {
                $this->output(self::OK, array('preview_file_url' => $preview_files_url . '/' . $new_filename), 'image saved');
            } else {
                $this->output(self::ERROR, null, 'image not saved');
            }
        }
    }
}

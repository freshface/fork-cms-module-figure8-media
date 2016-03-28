<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;

use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;

use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;

/**
 * This is the delete-action, it deletes an item
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class DeleteFile extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendMediaModel::existsFile($this->id)) {
            parent::execute();
            $record = (array) BackendMediaModel::getFile($this->id);

            BackendMediaModel::deleteFile($this->id);

            $files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;
            $preview_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;
            $poster_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_POSTER_FILES_FOLDER;
            $poster_preview_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_POSTER_PREVIEW_FILES_FOLDER;

            $fs = new Filesystem();

            if($record['filename']) $fs->remove($files_path . '/' . $record['filename']);
            if($record['original_filename']) $fs->remove($files_path . '/' . $record['original_filename']);
            if($record['filename']) $fs->remove($preview_files_path . '/' . $record['filename']);

            if($record['poster_filename']) $fs->remove($poster_files_path . '/' . $record['poster_filename']);
            if($record['poster_original_filename']) $fs->remove($poster_files_path . '/' . $record['poster_original_filename']);
            if($record['poster_filename']) $fs->remove($poster_preview_files_path . '/' . $record['poster_filename']);

            BackendMediaHelper::removeGeneratedFiles(FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_GENERATED_FILES_FOLDER, $record['filename']);


            $this->redirect(
                Model::createURLForAction('Index') . '&report=deleted&folder_id=' . $record['folder_id'] 
            );
        }
        else $this->redirect(Model::createURLForAction('Index') . '&error=non-existing');
    }
}

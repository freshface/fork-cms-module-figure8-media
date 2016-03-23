<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Modules\Media\Engine\TreeModel as BackendMediaTreeModel;

class RenameFolder extends BackendBaseAJAXAction
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
        $name = \SpoonFilter::getPostValue('name', null, '', 'string');

        // init validation
        $errors = array();

        // validate
        if ($id === 0) {
            $errors[] = 'no id provided';
        }

        if ($name === '') {
            $errors[] = 'no new name provided';
        }

        // got errors
        if (!empty($errors)) {
            $this->output(self::BAD_REQUEST, array('errors' => $errors), 'not all fields were filled');
        } else {
            
             $data = array(
                'id' => $id,
                'name' => $name
            );

            $success = BackendMediaTreeModel::renameFolder($data);

            // build cache
            BackendMediaTreeModel::deleteFolderTreeCache();
            BackendMediaTreeModel::getFolderTreeHTML();

            // output
            if ($success) {
                $this->output(self::OK, null, 'folder renamed');
            } else {
                $this->output(self::ERROR, null, 'folder not renamed');
            }
        }
    }
}

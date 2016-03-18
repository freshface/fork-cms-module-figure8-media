<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

class MoveFolder extends BackendBaseAJAXAction
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
        $dropped_on = \SpoonFilter::getPostValue('dropped_on', null, 0, 'int');

        // init validation
        $errors = array();

        // validate
        if ($id === 0) {
            $errors[] = 'no id provided';
        }

        // got errors
        if (!empty($errors)) {
            $this->output(self::BAD_REQUEST, array('errors' => $errors), 'not all fields were filled');
        } else {
            
             $data = array(
                'id' => $id,
                'dropped_on' => $dropped_on
            );

            $success = BackendMediaModel::moveFolder($data);

            // build cache
            BackendMediaModel::deleteFolderTreeHTMLCache();
            BackendMediaModel::getFolderTreeHTML();

            // output
            if ($success) {
                $this->output(self::OK, null, 'folder moved');
            } else {
                $this->output(self::ERROR, null, 'folder not moved');
            }
        }
    }
}

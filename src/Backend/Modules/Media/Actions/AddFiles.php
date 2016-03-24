<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionEdit;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Modules\Media\Engine\TreeModel as BackendMediaTreeModel;
use Backend\Core\Engine\Form as BackendForm;

/**
 * This is the index-action (default), it will display the overview of Media posts
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class AddFiles extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // add js
        $this->header->addJS('jquery.uploadifive.js');
        $this->header->addJS('MediaUploaderInit.js', null, false);

        // add css
        $this->header->addCSS('uploadifive.css');

        $this->getData();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }
    /**
     * Get the data
     */
    protected function getData()
    {
        $this->folder_id = $this->getParameter('folder_id', 'int');
        $this->tree = BackendMediaTreeModel::getFolderTreeHTML();
        $this->folder = BackendMediaTreeModel::getFolder($this->folder_id);
        $this->library = BackendMediaModel::getLibraryForFolder($this->folder_id);
        $this->allowed_file_types = BackendMediaModel::getAllAllowedFileTypesForJavascript();
    }

     /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

        $folders = BackendMediaTreeModel::getFolderTreeForDropdown();
        $this->frm->addDropdown('folder', $folders, $this->folder_id)->setDefaultElement('Select a folder', ''); 
    }

    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
             if ($this->frm->isCorrect()) {
             }
        }
    }

    /**
     * Parse the page
     */
    protected function parse()
    {   
        parent::parse();

        $this->tpl->assign("library", $this->library);
        $this->tpl->assign("folder", $this->folder);
        $this->tpl->assign("tree", $this->tree);
        $this->header->addJSData('media','folder_id', $this->folder_id);

        $timestamp = time();

        $this->header->addJSData('media','upload_timestamp', $timestamp);
        $this->header->addJSData('media','upload_token', md5($timestamp));
        $this->header->addJSData('media','allowed_file_types', $this->allowed_file_types);

        $this->header->addJSData('media','upload_uploaded_success_url', Model::createURLForAction('Index') . '&folder_id=' . $this->folder_id);
        $this->header->addJSData('media','upload_uploaded_fallback_url', ''); // not supported page

        $this->header->addJSData('media','add_files_url', Model::createURLForAction('AddFiles'));
    }
}

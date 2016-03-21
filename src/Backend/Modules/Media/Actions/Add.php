<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

/**
 * This is the index-action (default), it will display the overview of Media posts
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Add extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // add js
        $this->header->addJS('jquery.uploadifive.js');
        $this->header->addJS('MediaInit.js', null, false);

        // add css
        $this->header->addCSS('uploadifive.css');


        $this->getData();

        $this->parse();
        $this->display();
    }
    /**
     * Get the data
     */
    protected function getData()
    {
        $this->folder_id = $this->getParameter('folder_id', 'int');
        $this->tree = BackendMediaModel::getFolderTreeHTML();
        $this->folder = BackendMediaModel::getFolder($this->folder_id);
        $this->library = BackendMediaModel::getLibraryForFolder($this->folder_id);
        $this->allowed_file_types = BackendMediaModel::getAllAllowedFileTypesForJavascript();
    }


    /**
     * Parse the page
     */
    protected function parse()
    {   
        $this->tpl->assign("library", $this->library);
        $this->tpl->assign("folder", $this->folder);
        $this->tpl->assign("tree", $this->tree);
        $this->header->addJSData('media','folder_id', $this->folder_id);

        $timestamp = time();

        $this->header->addJSData('media','upload_timestamp', $timestamp);
        $this->header->addJSData('media','upload_token', md5($timestamp));
        $this->header->addJSData('media','allowed_file_types', $this->allowed_file_types);

        $this->header->addJSData('media','upload_uploaded_success_url', '');
        $this->header->addJSData('media','upload_uploaded_fallback_url', ''); // not supported page
    }
}

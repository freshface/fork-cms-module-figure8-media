<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Modules\Media\Engine\TreeModel as BackendMediaTreeModel;
use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;


/**
 * This is the index-action (default), it will display the overview of Media posts
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class EditFile extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->id = $this->getParameter('id', 'int');
        

        // does the item exists
        if ($this->id !== null && BackendMediaModel::existsFile($this->id)) {
            parent::execute();

             // add js
            $this->header->addJS('jquery.uploadifive.js');
            $this->header->addJS('MediaUploaderReplaceInit.js', null, false);
            $this->header->addJS('http://feather.aviary.com/imaging/v3/editor.js', null, false, true, false);
            $this->header->addJS('MediaEditFileInit.js', null, false);

            $this->header->addJS(FrontendMediaHelper::VIDEOJS_JS, null, false, true, false);
            $this->header->addCSS(FrontendMediaHelper::VIDEOJS_CSS, null, true, false, false);

            
            // add css
            $this->header->addCSS('uploadifive.css');

            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exception, because somebody is fucking with our URL
            $this->redirect(Model::createURLForAction('Index') . '&error=non-existing');
        }
    }
    /**
     * Get the data
     */
    protected function getData()
    {
        $this->languages = BackendMediaModel::getActiveLanguages();
        $this->record = BackendMediaModel::getFile($this->id);
        $this->allowed_file_types = BackendMediaModel::getAllAllowedFileTypesForJavascriptByType($this->record['type']);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

        $folders = BackendMediaTreeModel::getFolderTreeForDropdown();
        $this->frm->addDropdown('folder', $folders, $this->record['folder_id']); 

        foreach($this->languages as &$language)
        {
            $language['formElements']['txtName'] = $this->frm->addText('name_'. $language['abbreviation'], isset($this->record['content'][$language['abbreviation']]['name']) ? $this->record['content'][$language['abbreviation']]['name'] : '', null, 'inputText title');
            $language['formElements']['txtText'] = $this->frm->addEditor('text_'. $language['abbreviation'], isset($this->record['content'][$language['abbreviation']]['text']) ? $this->record['content'][$language['abbreviation']]['text'] : '');
        }

        
    }


    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('languages', $this->languages);
        $this->tpl->assign('record', $this->record);
        $this->tpl->assign('allow_feather_edit', in_array(strtolower($this->record['extension']), array('jpg','png')));

        $timestamp = time();

        $this->header->addJSData('media','upload_timestamp', $timestamp);
        $this->header->addJSData('media','upload_token', md5($timestamp));
        $this->header->addJSData('media','allowed_file_types', $this->allowed_file_types);

        $this->header->addJSData('media','id', $this->record['id']);


        $feather_api_key = $this->get('fork.settings')->get($this->URL->getModule(), 'feather_api_key', 'your api key');
        $this->header->addJSData('media','feather_api_key', $feather_api_key);
        $this->tpl->assign('feather_api_key', $feather_api_key);

        $this->header->addJSData('media','upload_uploaded_success_url', '');
        $this->header->addJSData('media','upload_uploaded_fallback_url', ''); // not supported page
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
             if ($this->frm->isCorrect()) {

                $item['id'] = $this->id;
                $item['folder_id'] = $this->frm->getField('folder')->getValue();
                $item['edited_on'] = Model::getUTCDate();

                $content = array();
                foreach($this->languages as $language)
                {
                    $specific['media_id'] = $item['id'];
                    $specific['language'] = $language['abbreviation'];
                    $specific['name'] = $this->frm->getField('name_'. $language['abbreviation'])->getValue();
                    $specific['text'] = ($this->frm->getField('text_'. $language['abbreviation'])->isFilled()) ? $this->frm->getField('text_'. $language['abbreviation'])->getValue() : null;
                    $content[$language['abbreviation']] = $specific;
                }

                BackendMediaModel::updateFile($item);
                BackendMediaModel::updateFileContent($content, $item['id']);

                $this->redirect(
                    Model::createURLForAction('EditFile') . '&report=saved&id=' . $this->record['id'] 
                );
             }
        }

    }
}

<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Core\Engine\Form as BackendForm;


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
            $this->header->addJS('MediaUploaderInit.js', null, false);

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
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

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
                BackendMediaModel::updateFileContent($content, $item['id'] );

             }
        }

    }
}

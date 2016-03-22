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
        $this->record = BackendMediaModel::getFile($this->id);

        // does the item exists
        if ($this->id !== null && !empty($this->record)) {
            parent::execute();


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
        
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');
    }


    /**
     * Parse the page
     */
    protected function parse()
    {

    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
             if ($this->frm->isCorrect()) {

             }
        }

    }
}

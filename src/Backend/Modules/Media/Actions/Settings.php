<?php

namespace Backend\Modules\Media\Actions;


use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;


class Settings extends BackendBaseActionEdit
{

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Loads the settings form
     */
    private function loadForm()
    {
        $this->isGod = BackendAuthentication::getUser()->isGod();

        $this->frm = new BackendForm('settings');

        $this->frm->addText('feather_api_key',  $this->get('fork.settings')->get($this->URL->getModule(), 'feather_api_key'), null, 'inputText input-wide', 'inputTextError input-wide');

    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {

            if ($this->frm->isCorrect()) {
                
                // set our settings
                $this->get('fork.settings')->set($this->URL->getModule(), 'feather_api_key', (string) $this->frm->getField('feather_api_key')->getValue());
               
             
                // redirect to the settings page
                $this->redirect(BackendModel::createURLForAction('Settings') . '&report=saved');
            }
        }
    }
}

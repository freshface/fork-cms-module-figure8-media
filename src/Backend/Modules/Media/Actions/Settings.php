<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

class Settings extends BackendBaseActionEdit
{
    private $dgResolutions;

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
        $this->loadDataGridResolutions();
        $this->tpl->assign('dgResolutions', (string) $this->dgResolutions->getContent());
    }

    private function loadDataGridResolutions()
    {
        $this->dgResolutions = new BackendDataGridDB(
            BackendMediaModel::QRY_DATAGRID_BROWSE_RESOLUTIONS,
            array('active', BL::getWorkingLanguage())
        );

     
        // add edit column
        $this->dgResolutions->addColumn(
            'edit',
            null,
            BL::lbl('Edit'),
            BackendModel::createURLForAction('EditResolution') .
            '&amp;id=[id]',
            BL::lbl('Edit')
        );
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

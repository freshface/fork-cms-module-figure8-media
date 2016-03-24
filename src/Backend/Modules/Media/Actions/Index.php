<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
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
class Index extends BackendBaseActionEdit
{
    /**
     * Filter variables
     *
     * @var array
     */
    private $filter;


    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // add js
        $this->header->addJS('jstree/jstree.min.js', null, false);
        $this->header->addJS('MediaTreeInit.js', null, true);

        // add css
        $this->header->addCSS('/src/Backend/Modules/Media/Js/jstree/themes/default/style.css', null, true);
        $this->header->addCSS('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', null, true, false, false);

        $this->setFilter();
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
        $this->folder_id = $this->filter['folder_id'];
        $this->tree = BackendMediaTreeModel::getFolderTreeHTML();
        $this->folder = BackendMediaTreeModel::getFolder($this->folder_id);
        $this->library = BackendMediaModel::getLibraryFiltered($this->filter);
    }


    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('filter', null, 'get', false);
       
        $types = array();
        $types[''] = ucfirst(Language::getLabel('AllMedia'));
        $types['image'] = ucfirst(Language::getLabel('Images'));
        $types['file'] = ucfirst(Language::getLabel('Documents'));
        $types['video'] = ucfirst(Language::getLabel('Videos'));
        $types['audio'] = ucfirst(Language::getLabel('Audio'));
        $this->frm->addDropdown('type', $types);

        $this->frm->addText('search')->setAttribute('placeholder', ucfirst(Language::getLabel('Search')));

        $views = array();
        $views[] = array('label' => '<i class="fa fa-th"></i>', 'value' => 'grid');
        $views[] = array('label' => '<i class="fa fa-list"></i>', 'value' => 'list');
        $this->frm->addRadiobutton('view', $views, $this->filter['view']);

        $this->frm->addHidden('folder_id', $this->filter['folder_id']);

        $this->frm_action = new BackendForm('action');
        $this->frm_action->addHidden('ids');
        $actions = array();
        $actions[] = array('label' => ucfirst(Language::getLabel('Delete')), 'value' => 'delete');
        $this->frm_action->addRadiobutton('actions', $actions);
       
    }

    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
             if ($this->frm->isCorrect()) {
             }
        }

        // is the form submitted?
        if ($this->frm_action->isSubmitted()) {
             if ($this->frm_action->isCorrect()) {
             }
        }
    }


    /**
     * Parse the page
     */
    protected function parse()
    {   
        parent::parse();
        $this->frm_action->parse($this->tpl);
        $this->tpl->assign("library", $this->library);
        $this->tpl->assign("folder", $this->folder);
        $this->tpl->assign("tree", $this->tree);
        $this->header->addJSData('media','folder_id', $this->folder_id);
    }

    private function setFilter()
    {
        // set filter values
        $this->filter['folder_id'] = $this->getParameter('folder_id', 'int');
        $this->filter['type'] = $this->getParameter('type', 'string');
        $this->filter['search'] = $this->getParameter('search', 'string');
        $this->filter['view'] = $this->getParameter('view', 'string', 'grid');
    }

}

<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

/**
 * This is the index-action (default), it will display the overview of Media posts
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Index extends ActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // add js
        $this->header->addJS('jstree/jstree.min.js', null, false);

        // add css
        $this->header->addCSS('/src/Backend/Modules/Media/Js/jstree/themes/default/style.css', null, true);

        $this->parse();
        $this->display();
    }


    /**
     * Parse the page
     */
    protected function parse()
    {
    }
}

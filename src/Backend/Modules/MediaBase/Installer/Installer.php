<?php

namespace Backend\Modules\MediaBase\Installer;

use Backend\Core\Installer\ModuleInstaller;


/**
 * Installer for the MediaBase module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Installer extends ModuleInstaller
{
    public function install()
    {
        // import the sql
        //$this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install the module in the database
        $this->addModule('MediaBase');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'MediaBase');
    }
}

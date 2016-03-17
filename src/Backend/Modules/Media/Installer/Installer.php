<?php

namespace Backend\Modules\Media\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the Media module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Installer extends ModuleInstaller
{
    public function install()
    {
        $db = $this->getDB();

        // import the sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install the module in the database
        $this->addModule('Media');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'Media');

        $this->setActionRights(1, 'Media', 'Index');
        $this->setActionRights(1, 'Media', 'Add');
        $this->setActionRights(1, 'Media', 'Edit');
        $this->setActionRights(1, 'Media', 'Delete');
        $this->setActionRights(1, 'Media', 'Upload');
        $this->setActionRights(1, 'Media', 'Delete');
        $this->setActionRights(1, 'Media', 'Settings');
        $this->setActionRights(1, 'Media', 'Widgets');
        $this->setActionRights(1, 'Media', 'EditAlbum');
        $this->setActionRights(1, 'Media', 'AddAlbum');
        $this->setActionRights(1, 'Media', 'DeleteAlbum');
        $this->setActionRights(1, 'Media', 'MoveFolder');
        $this->setActionRights(1, 'Media', 'RenameFolder');

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationModulesId = $this->setNavigation($navigationModulesId, 'Media', 'media/index');
        $navigationClassnameId = $this->setNavigation(
            $navigationModulesId,
            'Library',
            'media/index',
            array('media/add','media/edit')
        );

        $this->setNavigation(
            $navigationClassnameId,
            'Widgets',
            'media/widgets',
            array('media/add_widget', 'media/edit_widget')
        );


         // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Media', 'media/settings');

        $db->insert(
                'media_folders',
                array(
                    'id' => 1,
                    'name' => 'Default',
                    'count' => 0,
                    'parent_id' => 0
                )
            );

    }
}

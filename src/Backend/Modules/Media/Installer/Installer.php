<?php

namespace Backend\Modules\Media\Installer;

use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Photogallery\Engine\Api as Api;


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
        
        $this->setActionRights(1, 'Media', 'AddFiles');
        $this->setActionRights(1, 'Media', 'DeleteFile');
        $this->setActionRights(1, 'Media', 'EditFile');
        $this->setActionRights(1, 'Media', 'ImporttFile');

        $this->setActionRights(1, 'Media', 'Index');
        $this->setActionRights(1, 'Media', 'IndexMassAction');
        $this->setActionRights(1, 'Media', 'Settings');

        $this->setActionRights(1, 'Media', 'Albums');
        $this->setActionRights(1, 'Media', 'EditAlbum');
        $this->setActionRights(1, 'Media', 'AddAlbum');
        $this->setActionRights(1, 'Media', 'DeleteAlbum');

        $this->setActionRights(1, 'Media', 'CreateFolder');
        $this->setActionRights(1, 'Media', 'MoveFolder');
        $this->setActionRights(1, 'Media', 'RenameFolder');
        $this->setActionRights(1, 'Media', 'ReplaceFile');
        $this->setActionRights(1, 'Media', 'ReplacePosterFile');
        $this->setActionRights(1, 'Media', 'ResetModifiedImage');
        $this->setActionRights(1, 'Media', 'ResetModifiedPosterImage');
        $this->setActionRights(1, 'Media', 'SaveModifiedImage')
        $this->setActionRights(1, 'Media', 'SaveModifiedPosterImage');;
        $this->setActionRights(1, 'Media', 'Upload');

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationModulesId = $this->setNavigation($navigationModulesId, 'Media', 'media/index');
        $navigationClassnameId = $this->setNavigation(
            $navigationModulesId,
            'Library',
            'media/index',
            array('media/add_files','media/edit_file','media/import_file')
        );

        $this->setNavigation(
            $navigationModulesId,
            'Albums',
            'media/albums',
            array('media/add_widget', 'media/edit_widget')
        );


         // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Media', 'media/settings', array('media/edit_resolutions', 'media/add_resolution'));

        $db->insert(
                'media_folders',
                array(
                    'id' => 1,
                    'name' => 'Root',
                    'count' => 0,
                    'parent_id' => 0
                )
            );

        // Do API Call
        self::doApiCall();
    }


    private function doApiCall()
    {
        try
        {
            // build parameters
            $parameters = array(
                'site_domain' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'fork.local',
                'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
                'type' => 'module',
                'name' => 'Media',
                'version' => '1.0',
                'email' => \SpoonSession::get('email')
            );
        
            // call
            $api = new Api();
            $api->setApiURL('http://www.fork-cms-extensions.com/api/1.0');
            $return = $api->doCall('products.insertProductInstallation', $parameters, false);
        } 
        catch(Exception $e){}
    }

}

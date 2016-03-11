<?php

namespace Backend\Modules\MediaBase\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Form;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * In this file we store all generic functions that we will be using in the MediaBase module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Helper
{
	public static function getLanguages()
    {
    	$languages = array();

        foreach(Language::getActiveLanguages() as $abbreviation) {
        	$languages[] = array('abbreviation' => $abbreviation, 'label' => Language::getLabel(mb_strtoupper($abbreviation)));
        }

        return $languages;
    }

    public static function validateImage($frm, $field)
    {
	    if ($frm->getField($field)->isFilled()) {
		    // image extension and mime type
		    $frm->getField($field)->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), Language::err('JPGGIFAndPNGOnly'));
		    $frm->getField($field)->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), Language::err('JPGGIFAndPNGOnly'));
		}
	}

	public static function getImageUrl($image, $module, $folder = 'image', $size = '400x')
    {
    	return FRONTEND_FILES_URL . '/' . $module . '/' . $folder . '/' . $size . '/' . $image;
    }

    public static function generateFolders($module, $folder = 'image')
    {
    	// the image path
        $imagePath = FRONTEND_FILES_PATH . '/' . $module . '/' . $folder;

        $fs = new Filesystem();
        if (!$fs->exists($imagePath . '/source')) {
            $fs->mkdir($imagePath . '/source');
        }
        if (!$fs->exists($imagePath . '/400x')) {
            $fs->mkdir($imagePath . '/400x');
        }

         if (!$fs->exists($imagePath . '/800x')) {
            $fs->mkdir($imagePath . '/800x');
        }

         if (!$fs->exists($imagePath . '/1200x')) {
            $fs->mkdir($imagePath . '/1200x');
        }

        return $imagePath;
    }
}

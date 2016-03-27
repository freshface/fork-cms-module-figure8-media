<?php

namespace Backend\Modules\Media\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;
use Backend\Core\Engine\Language;

use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;

/**
 * In this file we store all generic functions that we will be using in the Media module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Helper
{

    public static function removeGeneratedFiles($filename)
    {
        $generated_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_GENERATED_FILES_FOLDER;
        $finder = new Finder();
        $fs = new Filesystem();
        $fs->mkdir($generated_files_path, 0775);
        foreach ($finder->directories()->in($generated_files_path) as $directory) {
            $fileName = $directory->getRealPath() . '/' . $filename;
            if (is_file($fileName)) {
                $fs->remove($fileName);
            }
        }
    }

    public static function generateThumbnail($filename, $source_path, $destination_path)
    {
        $thumbnail = new \SpoonThumbnail($source_path . '/' . $filename , 400, 400, true);
        $thumbnail->setAllowEnlargement(true);
        $thumbnail->setForceOriginalAspectRatio(true);
        $thumbnail->parseToFile($destination_path . '/' . $filename, 100);
    }
    
}

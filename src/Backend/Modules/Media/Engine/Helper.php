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
    // source https://github.com/lingtalfi/video-ids-and-thumbnails/blob/master/function.video.php
    public static function getVimeoId($url)
    {
        if (preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m)) {
            return $m[1];
        }
        return false;
    }

    public static function getYoutubeId($url)
    {
        $parts = parse_url($url);
        if (isset($parts['host'])) {
            $host = $parts['host'];
            if (
                false === strpos($host, 'youtube') &&
                false === strpos($host, 'youtu.be')
            ) {
                return false;
            }
        }
        if (isset($parts['query'])) {
            parse_str($parts['query'], $qs);
            if (isset($qs['v'])) {
                return $qs['v'];
            }
            else if (isset($qs['vi'])) {
                return $qs['vi'];
            }
        }
        if (isset($parts['path'])) {
            $path = explode('/', trim($parts['path'], '/'));
            return $path[count($path) - 1];
        }
        return false;
    }

    public static function removeGeneratedFiles($generated_files_path, $filename)
    {
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

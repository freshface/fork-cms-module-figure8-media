<?php

namespace Backend\Modules\Media\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language;
use Symfony\Component\Filesystem\Filesystem;

/**
 * In this file we store all generic functions that we will be using in the Media module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Model
{

    public static function getAllAllowedFileTypes()
    {
        return (array) BackendModel::get('database')->getRecords(
            'SELECT i.*
             FROM media_allowed_file_types AS i'
        );
    }

    public static function getAllAllowedFileMimetypes()
    {
        return (array) BackendModel::get('database')->getColumn(
            'SELECT i.mimetype
             FROM media_allowed_file_types AS i WHERE i.mimetype IS NOT NULL'
        );
    }

    public static function getAllAllowedTypeByMimetype($mimetype)
    {
        return (string) BackendModel::get('database')->getVar(
            'SELECT i.type
             FROM media_allowed_file_types AS i WHERE i.mimetype = ?', array($mimetype)
        );
    }

    public static function getAllAllowedFileTypesForJavascript()
    {
        $types = (array) BackendModel::get('database')->getColumn(
            'SELECT i.mimetype
             FROM media_allowed_file_types AS i WHERE i.mimetype IS NOT NULL'
        );

        return implode(',', $types);
    }

    public static function getFolderMaximumSequence()
    {
        return (int) BackendModel::get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM media_folders AS i'
        );
    }

    public static function moveFolder($data)
    {
        BackendModel::get('database')->update(
            'media_folders', array('parent_id' => $data['dropped_on']), 'id = ?', (int) $data['id']
        );

        return true;
    }

    public static function renameFolder($data)
    {
        BackendModel::get('database')->update(
            'media_folders', array('name' => $data['name']), 'id = ?', (int) $data['id']
        );

        return true;
    }

    public static function insertFile($data)
    {
         return (int) BackendModel::get('database')->insert('media_library', array($data));
    }

    public static function createFolder($data)
    {
         return (int) BackendModel::get('database')->insert('media_folders', array($data));
    }

    public static function folderArrayToFlatArray()
    {
       $array = self::getFolderArray();
       $return = array();

       foreach($array as $item){
       
            $return[$item['id']] = $item['parent_id'];
            $return[$item['id']] = array('name' => $item['name'], 'parent_id' => $item['parent_id'], 'id' => $item['id']);
       }

        return $return;
    }

    public static function deleteFolderTreeHTMLCache()
    {
        $fs = new Filesystem();
        $fs->remove(BACKEND_CACHE_PATH . '/Media/tree-ul.tpl');
    }


    public static function getFolderTreeHTML()
    { 
         if (!is_file(BACKEND_CACHE_PATH . '/Media/tree-ul.tpl')) {

            $value = self::folderArrayTreeToList(self::flatFolderArrayToArrayTree(self::folderArrayToFlatArray()));
            $fs = new Filesystem();
            $fs->dumpFile(
                BACKEND_CACHE_PATH . '/Media/tree-ul.tpl',
                $value
            );
        }

        return file_get_contents(BACKEND_CACHE_PATH . '/Media/tree-ul.tpl');
    }


    public static function folderArrayTreeToList($tree)
    {       
        $return = '<ul>';

        foreach($tree as $node) {
            $return .= '<li>';
            $return .= '<a  href="#" data-id="' . $node['id'] .'">';
            $return .= $node['name'];
             $return .= '</a>';
                if(isset($node['children'])) $return .= self::folderArrayTreeToList($node['children']);
            $return .= '</li>';
        }

        $return .= '</ul>';
        
        return $return;
    }

    public static function flatFolderArrayToArrayTree($array)
    {
        $flat = array();
        $tree = array();

        foreach ($array as $child => $parent) {
            if (!isset($flat[$child])) {
                $flat[$child] = array('name' => $parent['name'], 'id' => $parent['id']);
            }
            if (!empty($parent['parent_id'])) {
                $flat[$parent['parent_id']]['children'][$child] =& $flat[$child];
            } else {
                $tree[$child] =& $flat[$child];
            }
        }

        return $tree;
    }

    public static function getFolderArray($parent = 0, $return = array())
    {
        $result = (array) BackendModel::get('database')->getRecords(
            'SELECT i.name, i.parent_id, i.id
             FROM media_folders AS i
             WHERE i.parent_id = ? ORDER BY i.sequence',
            array((int) $parent), 'id'
        );

        foreach($result as $row){
            $return[] = array('id' => $row['id'], 'parent_id' => $row['parent_id'], 'name' => $row['name']);
            $return = self::getFolderArray($row['id'], $return);
        }
        
        return $return;
    }


    public static function getFolder($id)
    {
        $db = BackendModel::get('database');

        $return =  (array) $db->getRecord(
            'SELECT i.*
             FROM media_folders AS i
             WHERE i.id = ?',
            array((int) $id)
        );

        return  $return;
    }

    public static function getLibraryForFolder($id)
    {
        $db = BackendModel::get('database');

        $return =  (array) $db->getRecord(
            'SELECT i.*
             FROM media_library AS i
             WHERE i.folder_id = ?',
            array((int) $id)
        );

        return  $return;

    }

    public static function getFilename($filename, $id = null)
    {
        $filename = (string) $filename;

       $path_parts = pathinfo($filename);

        // get db
        $db = BackendModel::getContainer()->get('database');

        // new item
        if ($id === null) {
            // already exists
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM media_library AS i
                 WHERE i.filename  = ?
                 LIMIT 1',
                array($filename)
            )
            ) {
                $filename = BackendModel::addNumber($path_parts['filename']) . '.' . $path_parts['extension'];

                return self::getFilename($filename);
            }
        } else {
            // current category should be excluded
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM media_library AS i
                 WHERE i.filename = ? AND i.id != ?
                 LIMIT 1',
                array( $filename, $id)
            )
            ) {
                $filename = BackendModel::addNumber($path_parts['filename']) . '.' . $path_parts['extension'];

                return self::getFilename($filename, $id);
            }
        }

        return $filename;
    }
}

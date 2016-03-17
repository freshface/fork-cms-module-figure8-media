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

    public static function moveFolder($id, $droppedOn)
    {
        BackendModel::get('database')->update(
            'media_folders', array('parent_id' => $droppedOn), 'id = ?', (int) $id
        );

        return true;
    }

    public static function renameFolder($id, $name)
    {
        BackendModel::get('database')->update(
            'media_folders', array('name' => $name), 'id = ?', (int) $id
        );

        return true;
    }

     public static function createFolder($parent_id, $name)
    {
         return (int) BackendModel::get('database')->insert('media_folders', array('parent_id' => (int )$parent_id, 'name' => $name));
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
        $fs->remove(BACKEND_CACHE_PATH . '/Media/tree.tpl');
    }


    public static function getFolderTreeHTML()
    { 
         if (!is_file(BACKEND_CACHE_PATH . '/Media/tree.tpl')) {

            $value = self::folderArrayTreeToList(self::flatFolderArrayToArrayTree(self::folderArrayToFlatArray()));
            $fs = new Filesystem();
            $fs->dumpFile(
                BACKEND_CACHE_PATH . '/Media/tree.tpl',
                $value
            );
        }

        return file_get_contents(BACKEND_CACHE_PATH . '/Media/tree.tpl');
    }


    public static function folderArrayTreeToList($tree)
    {       
        $return = '<ul>';

        foreach($tree as $node) {
            $return .= '<li>';
            $return .= '<a href="#" id="folder-' . $node['id'] . '" data-id="' . $node['id'] .'">';
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
             WHERE i.parent_id = ? ORDER BY i.name',
            array((int) $parent), 'id'
        );

        foreach($result as $row){
            $return[] = array('id' => $row['id'], 'parent_id' => $row['parent_id'], 'name' => $row['name']);
            $return = self::getFolderArray($row['id'], $return);
        }
        
        return $return;
    }


 

    /**
     * Delete a certain item
     *
     * @param int $id
     */
    public static function delete($id)
    {
        BackendModel::get('database')->delete('media', 'id = ?', (int) $id);
        BackendModel::get('database')->delete('shop_product_content', 'brand_id = ?', (int) $id);
        BackendModel::get('database')->update('media', array('brand_id' => NULL), 'brand_id = ?', array($id));
    }

    /**
     * Checks if a certain item exists
     *
     * @param int $id
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::get('database')->getVar(
            'SELECT 1
             FROM media AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Fetches a certain item
     *
     * @param int $id
     * @return array
     */
    public static function get($id)
    {
        $db = BackendModel::get('database');

        $return =  (array) $db->getRecord(
            'SELECT i.*
             FROM media AS i
             WHERE i.id = ?',
            array((int) $id)
        );

        // data found
        $return['content'] = (array) $db->getRecords(
            'SELECT i.* FROM shop_product_content AS i
            WHERE i.brand_id = ?',
            array((int) $id), 'language');

        return  $return;

    }


    /**
     * Insert an item in the database
     *
     * @param array $item
     * @return int
     */
    public static function insert(array $item)
    {
        $item['created_on'] = BackendModel::getUTCDate();
        $item['edited_on'] = BackendModel::getUTCDate();

        return (int) BackendModel::get('database')->insert('media', $item);
    }

    public static function insertContent(array $content)
    {
        BackendModel::get('database')->insert('shop_product_content', $content);
    }

    /**
     * Updates an item
     *
     * @param array $item
     */
    public static function update(array $item)
    {
        $item['edited_on'] = BackendModel::getUTCDate();

        BackendModel::get('database')->update(
            'media', $item, 'id = ?', (int) $item['id']
        );
    }

    public static function updateContent(array $content, $id)
    {
        $db = BackendModel::get('database');
        foreach($content as $language => $row)
        {
            $db->update('shop_product_content', $row, 'brand_id = ? AND language = ?', array($id, $language));
        }
    }
}

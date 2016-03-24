<?php

namespace Backend\Modules\Media\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Language;

use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;

/**
 * In this file we store all generic functions that we will be using in the Media module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Model
{

    public static function getActiveLanguages()
    {
        $languages = array();

        foreach(Language::getActiveLanguages() as $abbreviation) {
            $languages[] = array('abbreviation' => $abbreviation, 'label' => Language::getLabel(mb_strtoupper($abbreviation)));
        }

        return $languages;
    }

     public static function getAllLanguages()
    {
        $all_languages = 'a:13:{i:0;s:2:"en";i:1;s:2:"zh";i:2;s:2:"nl";i:3;s:2:"fr";i:4;s:2:"de";i:5;s:2:"el";i:6;s:2:"hu";i:7;s:2:"it";i:8;s:2:"lt";i:9;s:2:"ru";i:10;s:2:"es";i:11;s:2:"sv";i:12;s:2:"uk";}';

        $all_languages = unserialize($all_languages);
        $languages = array();

        foreach($all_languages as $abbreviation) {
            $languages[] = array('abbreviation' => $abbreviation, 'label' => Language::getLabel(mb_strtoupper($abbreviation)));
        }

        return $languages;
    }

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

    public static function getAllAllowedFileMimetypesByType($type)
    {
        return (array) BackendModel::get('database')->getColumn(
            'SELECT i.mimetype
             FROM media_allowed_file_types AS i WHERE i.mimetype IS NOT NULL AND i.type = ?', array($type)
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
     public static function getAllAllowedFileTypesForJavascriptByType($type)
    {
        $types = (array) BackendModel::get('database')->getColumn(
            'SELECT i.mimetype
             FROM media_allowed_file_types AS i WHERE i.mimetype IS NOT NULL AND i.type = ?', array((string) $type)
        );

        return implode(',', $types);
    }

    public static function insertFile($data)
    {
         return (int) BackendModel::get('database')->insert('media_library', array($data));
    }

    public static function updateFile($data)
    {
       BackendModel::get('database')->update(
            'media_library', $data, 'id = ?', (int) $data['id']
        );

       return true;
    }

    public static function deleteFile($id)
    {
        BackendModel::get('database')->delete('media_library', 'id = ?', (int) $id);
        BackendModel::get('database')->delete('media_library_content', 'media_id = ?', (int) $id);
        BackendModel::get('database')->delete('media_linked_album_media', 'media_id = ?', (int) $id);
    }

    public static function existsFile($id)
    {
        return (bool) BackendModel::get('database')->getVar(
            'SELECT 1
             FROM media_library AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    public static function getFile($id)
    {
        $db = BackendModel::get('database');

        $return =  (array) $db->getRecord(
            'SELECT i.*
             FROM media_library AS i
             WHERE i.id = ?',
            array((int) $id)
        );

        $files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;
        $preview_files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;
        
        $return['data'] = @unserialize($return['data']);
        $return['is_' . $return['type']] = true;
        $return['is_modified'] = $return['modified'] == 'Y';
        $return['file_url'] = $files_path . '/' . $return['filename'];
        $return['preview_file_url'] = $preview_files_path . '/' . $return['filename'];
        
         // data found
        $return['content'] = (array) $db->getRecords(
            'SELECT i.* FROM media_library_content AS i
            WHERE i.media_id = ?',
            array((int) $id), 'language');

        return  $return;
    }

    public static function insertFileContent(array $content)
    {
        BackendModel::get('database')->insert('media_library_content', $content);
    }

    public static function updateFileContent(array $content, $id)
    {
        $db = BackendModel::get('database');
        foreach($content as $language => $row)
        {
            $db->update('media_library_content', $row, 'media_id = ? AND language = ?', array($id, $language));
        }
    }

    public static function getLibraryFiltered($filter = array())
    {
        $db = BackendModel::get('database');

        $parameters = array();
        $query = 'SELECT i.*, UNIX_TIMESTAMP(i.edited_on) as edited_on, c.name FROM media_library AS i INNER JOIN media_library_content AS c ON c.media_id = i.id AND c.language = ?';
        $parameters[] = Language::getWorkingLanguage();

        $query .= ' WHERE 1';

        if($filter['folder_id'] !== null)
        {
            $query .= ' AND i.folder_id = ?';
            $parameters[] = (int) $filter['folder_id'];
        }

        if($filter['type'] !== null)
        {
            $query .= ' AND i.type = ?';
            $parameters[] = (string) $filter['type'];
        }

        if($filter['search'] !== null)
        {
            $query .= ' AND (i.original_filename LIKE ?';
            $parameters[] = (string) '%' . $filter['search'] . '%';

            $query .= ' OR c.name LIKE ?';
            $parameters[] = (string) '%' . $filter['search'] . '%';

            $query .= ' OR c.text LIKE ?)';
            $parameters[] = (string) '%' . $filter['search'] . '%';
        }

        $return = (array) $db->getRecords($query, $parameters);

         // no results?
        if (empty($return)) {
            return array();
        }

        $edit_url = BackendModel::createURLForAction('EditFile');
        $files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;
        $preview_files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;

        foreach ($return as &$record){
            $record['data'] = @unserialize($record['data']);
            $record['edit_url'] = $edit_url . '/&id=' . $record['id'];
            $record['is_' . $record['type']] = true;
            $record['file_url'] = $files_path . '/' . $record['filename'];
            $record['preview_file_url'] = $preview_files_path . '/' . $record['filename'];
        }

        return  $return;
    }


    public static function getLibraryForFolder($id)
    {
        $db = BackendModel::get('database');

        $return =  (array) $db->getRecords(
            'SELECT i.*
             FROM media_library AS i
             WHERE i.folder_id = ? ORDER BY i.edited_on DESC',
            array((int) $id)
        );

        $edit_url = BackendModel::createURLForAction('EditFile');
        $files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;
        $preview_files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;

        foreach ($return as &$record){
            $record['data'] = @unserialize($record['data']);
            $record['edit_url'] = $edit_url . '/&id=' . $record['id'];
            $record['is_' . $record['type']] = true;
            $record['file_url'] = $files_path . '/' . $record['filename'];
            $record['preview_file_url'] = $preview_files_path . '/' . $record['filename'];
        }

        return  $return;
    }

    public static function getLibrary()
    {
        $db = BackendModel::get('database');

        $return =  (array) $db->getRecords(
            'SELECT i.*
             FROM media_library AS i
             WHERE 1 ORDER BY i.edited_on DESC',
            array()
        );

        $edit_url = BackendModel::createURLForAction('EditFile');
        $files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;
        $preview_files_path = FRONTEND_FILES_URL . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;

        foreach ($return as &$record){
            $record['data'] = @unserialize($record['data']);
            $record['edit_url'] = $edit_url . '/&id=' . $record['id'];
            $record['is_' . $record['type']] = true;
            $record['file_url'] = $files_path . '/' . $record['filename'];
            $record['preview_file_url'] = $preview_files_path . '/' . $record['filename'];
        }

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

<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionEdit;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Modules\Media\Engine\TreeModel as BackendMediaTreeModel;
use Backend\Core\Engine\Form as BackendForm;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;

use Common\Uri as CommonUri;

/**
 * This is the index-action (default), it will display the overview of Media posts
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class ImportFile extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // add js
        $this->header->addJS('ImportFileInit.js', null, false);

        $this->getData();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }
    /**
     * Get the data
     */
    protected function getData()
    {
        $this->folder_id = $this->getParameter('folder_id', 'int');
        $this->folder = BackendMediaTreeModel::getFolder($this->folder_id);
        $this->library = BackendMediaModel::getLibraryForFolder($this->folder_id);
        $this->languages = BackendMediaModel::getAllLanguages();
    }

     /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('import');

        $folders = BackendMediaTreeModel::getFolderTreeForDropdown();
        $this->frm->addDropdown('folder', $folders, $this->folder_id)->setDefaultElement('Select a folder', '');

        $sources = array();
        $sources['youtube'] = 'Youtube';
        $sources['vimeo'] = 'Vimeo';
        $this->frm->addDropdown('source', $sources)->setDefaultElement('Select a source', '');
        $this->frm->addText('url', null, null, 'inputText input-wide', 'inputTextError input-wide');
    }

    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {

            $this->frm->getField('source')->isFilled(Language::err('FieldIsRequired'));

            if($this->frm->getField('source')->getValue() == '') $this->frm->getField('source')->addError(Language::err('FieldIsRequired'));
            if($this->frm->getField('url')->isFilled(Language::err('FieldIsRequired'))) {
                $this->frm->getField('url')->isURL(Language::err('InvalidURL'));
            }

             if ($this->frm->isCorrect()) {

                $source = $this->frm->getField('source')->getValue();
                $url = $this->frm->getField('url')->getValue();

                // path to folder
                $files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_POSTER_FILES_FOLDER;
                $preview_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_POSTER_PREVIEW_FILES_FOLDER;
                
                $fs = new Filesystem();
                $fs->mkdir($files_path, 0775);
                $fs->mkdir($preview_files_path, 0775);

                $insert = array();
                $insert['type'] = 'video';
                $insert['data'] = serialize(array());
                $insert['folder_id'] = $this->folder_id;
                $insert['created_on'] = BackendModel::getUTCDate();
                $insert['edited_on'] = BackendModel::getUTCDate();
                $insert['modified'] = 'N';
                $insert['imported'] = 'Y';


                if($source == 'youtube') {
                     $video_id = BackendMediaHelper::getYoutubeId($url);

                    if($video_id)
                    {

                        $filename = BackendMediaModel::getPosterFilename(CommonUri::getUrl($video_id) . '.jpg');
                        if(\SpoonFile::download('http://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg',  $files_path . '/' . $filename)){

                            chmod($files_path . '/' . $filename, 0775);
                            $insert['poster_filename'] = $filename;
                            $insert['poster_original_filename'] = $filename;
                            $insert['poster_extension']  = 'jpg';
                            BackendMediaHelper::generateThumbnail($filename, $files_path, $preview_files_path);

                            list($width, $height) = getimagesize($files_path . '/' . $filename);

                            $data['poster_portrait'] = ($width > $height) ? false : true;

                        } 
                        

                        $data['source'] = $source;
                        $data['is_' . $source] = true;
                        $data['source_url'] = $url;
                        $data['source_id'] = $video_id;
                        $insert['data'] = serialize($data);
                        $insert['id'] = BackendMediaModel::insertFile($insert);

                        $content = array();
                        foreach($this->languages as $language)
                        {
                            $specific['media_id'] = $insert['id'];
                            $specific['language'] = $language['abbreviation'];
                            $specific['name'] = '';
                            $specific['text'] = '';
                            $content[$language['abbreviation']] = $specific;
                        }

                         BackendMediaModel::insertFileContent($content);

                        $this->redirect(
                            BackendModel::createURLForAction('Index') . '&report=imported&folder_id=' . $this->folder_id
                        );

                    } else {
                         $this->frm->getField('url')->setError(Language::err('InvalidURL'));
                    }
                }

                if($source == 'vimeo') {

                    $video_id = BackendMediaHelper::getVimeoId($url);
                    $name = '';
                    $description = '';

                    if($video_id)
                    {
                        $json = file_get_contents("http://vimeo.com/api/v2/video/$video_id.json");
                        $json = json_decode($json);

                        //\SPoon::dump($json );

                        if(isset($json[0]) && isset($json[0]->thumbnail_large)) {
                            $filename = BackendMediaModel::getPosterFilename(CommonUri::getUrl($video_id) . '.jpg');
                            if(\SpoonFile::download($json[0]->thumbnail_large,  $files_path . '/' . $filename)){

                                chmod($files_path . '/' . $filename, 0775);
                                $insert['poster_filename'] = $filename;
                                $insert['poster_original_filename'] = $filename;
                                $insert['poster_extension']  = 'jpg';

                                BackendMediaHelper::generateThumbnail($filename, $files_path, $preview_files_path);

                                list($width, $height) = getimagesize($files_path . '/' . $filename);

                                $data['poster_portrait'] = ($width > $height) ? false : true;

                            } 
                        }

                        if(isset($json[0]) && isset($json[0]->title)) {
                            $name = $json[0]->title;
                        }
                         if(isset($json[0]) && isset($json[0]->description)) {
                            $description = $json[0]->description;
                        }

                        $data['source'] = $source;
                        $data['is_' . $source] = true;
                        $data['source_url'] = $url;
                        $data['source_id'] = $video_id;
                        $insert['data'] = serialize($data);
                        $insert['id'] = BackendMediaModel::insertFile($insert);

                        $content = array();
                        foreach($this->languages as $language)
                        {
                            $specific['media_id'] = $insert['id'];
                            $specific['language'] = $language['abbreviation'];
                            $specific['name'] = $name;
                            $specific['text'] = $description;
                            $content[$language['abbreviation']] = $specific;
                        }

                         BackendMediaModel::insertFileContent($content);

                        $this->redirect(
                            BackendModel::createURLForAction('Index') . '&report=imported&folder_id=' . $this->folder_id
                        );

                    } else {
                         $this->frm->getField('url')->setError(Language::err('InvalidURL'));
                    }
                }
            }
        }
    }

    /**
     * Parse the page
     */
    protected function parse()
    {   
        parent::parse();

        $this->tpl->assign("folder", $this->folder);
        $this->header->addJSData('media','folder_id', $this->folder_id);

        $this->header->addJSData('media','import_file_url', BackendModel::createURLForAction('ImportFile'));
    }
}

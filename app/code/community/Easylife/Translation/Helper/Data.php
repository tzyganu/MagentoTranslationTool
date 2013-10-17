<?php 
/**
 * Easylife_Translation extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE_EASY_TRANSLATION.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Easylife
 * @package        Easylife_Translation
 * @copyright      Copyright (c) 2013
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Translation default helper
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * get helper alias for module
     * @access public
     * @param $module
     * @param $xml
     * @return int|null|string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getHelperAlias($module, $xml) {
        $helper = null;
        $node = $xml->global->helpers;
        if ($node) {
            foreach ((array)$node as $key=>$value) {
                if (isset($value['class'])) {
                    $helper = $key;
                }
            }
        }
        if (is_null($helper)) {
            $parts = explode('_', $module);
            if (isset($parts[1])) {
                $helper = strtolower($parts[1]);
            }
        }
        return $helper;
    }

    /**
     * get module files
     * @access public
     * @param $module
     * @param $xml
     * @param $codepool
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getModuleFiles($module, $xml, $codepool) {
        $files = array_merge(
            $this->getCodeFiles($module, $codepool),
            $this->getFiles($xml, 'frontend'),
            $this->getFiles($xml, 'adminhtml')
        );
        $return = array();
        foreach ($files as $file){
            $return[trim($file)] = 1;
        }
        return array_keys($return);
    }

    /**
     * get the app/code files for a module
     * @access public
     * @param $module
     * @param $codepool
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getCodeFiles($module, $codepool){
        $files = array();
        $files[] = 'app/code/'.$codepool.'/'.str_replace('_', '/', $module).'/';
        $checkIn = array('Block', 'controller', 'Model', 'Helper');
        $parts = explode('_', $module);
        $partModuleName = $parts[count($parts) - 1];
        foreach ($checkIn as $check){
            $possible = array(
                'app/code/core/Mage/Adminhtml/'.$check.'/'.$partModuleName=>'app/code/core/Mage/Adminhtml/'.$check.'/'.$partModuleName.'/',
                'app/code/core/Mage/Adminhtml/'.$check.'/'.$partModuleName.'.php'=>'app/code/core/Mage/Adminhtml/'.$check.'/'.$partModuleName.'.php',
                'app/code/core/Mage/Adminhtml/'.$check.'/'.$partModuleName.'Controller.php'=>'app/code/core/Mage/Adminhtml/'.$check.'/'.$partModuleName.'Controller.php',
                'app/design/adminhtml/default/default/layout/'.strtolower($partModuleName).'.xml'=>'app/design/adminhtml/*/*layout/'.strtolower($partModuleName).'.xml',
                'app/design/adminhtml/default/default/template/'.strtolower($partModuleName).'/'=>'app/design/adminhtml/*/*/template/'.strtolower($partModuleName).'/'
            );
            foreach ($possible as $realFile=>$aliasFile){
                if (file_exists($realFile)){
                    $files[] = $aliasFile;
                }
            }
        }
        sort($files);
        $files = array_unique($files);
        return $files;
    }
    /**
     * parse config.xml to get the needed layout and template files
     * @access public
     * @param $xml
     * @param $area
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getFiles($xml, $area) {
        $files = array();
        $layoutFiles = $this->getLayoutFiles($xml, $area);
        $themes = $this->getThemes($area);
        foreach ($layoutFiles as $file) {
            foreach ($themes as $theme) {
                $selectFileName = 'app/design/'.$area.'/*/*/layout/'.$file;
                $fileName = $theme.'/'.'layout'.'/'.$file;
                if (file_exists($fileName)) {
                    $files[$selectFileName] = 1;
                    //try to match the templates referenced in the layout file
                    $templateFolders = $this->getTemplateFolders($fileName);
                    foreach ($templateFolders as $folder) {
                        $selectFolderName = 'app/design/'.$area.'/*/*/template/'.$folder;
                        $folderName = $theme.'/'.'template'.'/'.$folder;
                        if (file_exists($folderName)) {
                            if (is_dir($folderName)) {
                                $folderName .= '/';
                                $selectFolderName .= '/';
                            }
                            $files[$selectFolderName] = 1;
                        }
                    }
                }
            }
        }
        return array_keys($files);
    }

    /**
     * get layout file from xml
     * @access public
     * @param $xml
     * @param $area
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getLayoutFiles($xml, $area) {
        $path = $xml->$area;
        if ($path){
            $path = $path->layout->updates;
        }
        $layoutFiles = array();
        foreach ((array)$path as $key=>$value) {
            $layoutFiles[] = (string)$value->file;
        }
        return $layoutFiles;
    }

    /**
     * get themes for area
     * @access public
     * @param $area
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getThemes($area) {
        if (!isset($this->_themes[$area])) {
            $this->_themes[$area] = glob('app'.'/'.'design'.'/'.$area.'/' .'*'.'/'.'*');
        }
        return $this->_themes[$area];
    }

    /**
     * get template folders
     * @access public
     * @param $layoutFile
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getTemplateFolders($layoutFile) {
        $folders = array();
        if (!file_exists($layoutFile)) {
            return $folders;
        }
        $xmlContent = file_get_contents($layoutFile);
        $xmlData = new SimpleXMLElement($xmlContent);
        $withTemplate = $xmlData->xpath('//*[@template]');
        foreach ($withTemplate as $element){
            $template = $element['template'];
            $parts = explode('/',$template);
            $folders[$parts[0]] = 1;//may also be a file
        }
        return array_keys($folders);
    }

    /**
     * merge an array of translated texts
     * @access public
     * @param $arr1
     * @param $arr2
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function mergeTexts($arr1, $arr2) {
        foreach ($arr2 as $key=>$values) {
            if (isset($arr1[$key])) {
                $arr1[$key] = array_merge($arr1[$key], $values);
            }
            else{
                $arr1[$key] = $values;
            }
        }
        return $arr1;
    }

    /**
     * get the temporary directory for storing files
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getTempDir() {
        return Mage::getBaseDir('var').'/translation/';
    }

    /**
     * setter to ignore not found helpers
     * access public
     * @param $flag
     * @return Easylife_Translation_Helper_Data
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function setIgnoreHelpers($flag) {
        $this->_ignoreHelpers = $flag;
        return $this;
    }

    /**
     * getter for ignore helpers
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getIgnoreHelpers() {
        return $this->_ignoreHelpers;
    }
}
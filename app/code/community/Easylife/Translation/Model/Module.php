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
 * Module model
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Model_Module extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY= 'translation_module';
    const CACHE_TAG = 'translation_module';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'translation_module';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'module';

    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function _construct() {
        parent::_construct();
        $this->_init('translation/module');
    }
    /**
     * before save module
     * @access protected
     * @return Easylife_Translation_Model_Module
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _beforeSave() {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get files to exclude
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getExcludedFiles(){
        $toExclude = array();
        $excluded = $this->getExclude(true);
        foreach ($excluded as $file){
            if (is_file($file)){
                $toExclude = $file;
            }
        }
        return $toExclude;
    }

    /**
     * get files to parse
     * @access public
     * @param bool $asArray
     * @return array|mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getFiles($asArray = false){
        $files = $this->getData('files');
        if (!$asArray){
            return $files;
        }
        return explode("\n", $files);
    }

    /**
     * get excluded files
     * @access public
     * @param bool $asArray
     * @return array|mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getExclude($asArray = false){
        $files = trim($this->getData('exclude'));
        if (!$asArray){
            return $files;
        }
        if (empty($files)){
            return array();
        }
        return explode("\n", $files);
    }

    /**
     * merge texts
     * @access public
     * @param $arr1
     * @param $arr2
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function mergeTexts($arr1, $arr2){
        return Mage::helper('translation')->mergeTexts($arr1, $arr2);
    }

    /**
     * get files separated by <br />
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getFilesBr(){
        return implode('<br />', $this->getFiles(true));
    }
}
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
 * Zip writer
 * 
 * @category    Easylife
 * @package        Easylife_Translation
 * @author         Marius Strajeru <marius.strajeru@gmail.com>
 */   
class Easylife_Translation_Model_Writer extends Mage_Connect_Package_Writer{
    /**
     * prefix for path
     * @var string
     */
    const PATH_TO_TEMPORARY_DIRECTORY = '../';
    protected $_pathPrefix = '';
    /**
     * constructor
     * @access public
     * @param array $files
     * @param mixed $namePackage
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function __construct($files, $namePackage=''){
        parent::__construct($files, $namePackage='');
        $this->_pathPrefix = '';
    }
    /**
     * build the package
     * @access public
     * @return Easylife_Translation_Model_Writer
     * @see Mage_Connect_Package_Writer::composePackage()
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function composePackage(){
         @mkdir(self::PATH_TO_TEMPORARY_DIRECTORY, 0777, true);
        $root = Mage::helper('translation')->getTempDir().basename($this->_namePackage);
        @mkdir($root, 0777, true);
        foreach ($this->_files as $file) {
            if (is_dir($file) || is_file($file)) {
                $fileName = basename($file);
                $filePath = dirname($file);
                if (substr($filePath, 0, strlen($this->_pathPrefix)) == $this->_pathPrefix){
                    $filePath = substr($filePath, strlen($this->_pathPrefix));
                }
                @mkdir($root . DS . $filePath, 0777, true);
                if (is_file($file)) {
                    copy($file, $root . DS . $filePath . DS . $fileName);
                }
                else {
                    @mkdir($root . DS . $filePath . $fileName, 0777);
                }
            }
        }

        $this->_temporaryPackageDir = $root;
        return $this;
     }
     /**
      * set the package name
      * @access public
      * @return Easylife_Translation_Model_Writer
      * @author Marius Strajeru <marius.strajeru@gmail.com>
      */
     public function setNamePackage($name){
         $this->_namePackage = $name;
         return $this;
     }
}
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
 * Module admin block
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Block_Adminhtml_Module extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function __construct(){
        $this->_controller         = 'adminhtml_module';
        $this->_blockGroup         = 'translation';
        $this->_headerText         = Mage::helper('translation')->__('Module');
        $this->_addButtonLabel     = Mage::helper('translation')->__('Add Module');

        $this->_addButton('import_modman', array(
            'label'     => Mage::helper('translation')->__('import via Modman'),
            'onclick'   => 'setLocation(\'' . $this->getImportUrl('modman') .'\')',
            'class'     => 'add',
        ));

        parent::__construct();
    }
    /**
     * build import url
     * @access public
     * @param $method string
     * @return string
     * @author Sander Mangel <sander@sandermangel.nl>
     */
    public function getImportUrl($method)
    {
        return $this->getUrl('*/translation_import/'.$method);
    }
}

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
 * Module admin edit tabs
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Block_Adminhtml_Module_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function __construct(){
        parent::__construct();
        $this->setId('module_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('translation')->__('Module'));
    }
    /**
     * before render html
     * @access protected
     * @return Easylife_Translation_Block_Adminhtml_Module_Edit_Tabs
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _beforeToHtml(){
        $this->addTab('form_module', array(
            'label'        => Mage::helper('translation')->__('Module'),
            'title'        => Mage::helper('translation')->__('Module'),
            'content'     => $this->getLayout()->createBlock('translation/adminhtml_module_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
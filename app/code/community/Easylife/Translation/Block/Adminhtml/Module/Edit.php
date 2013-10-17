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
 * Module admin edit block
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Block_Adminhtml_Module_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constuctor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'translation';
        $this->_controller = 'adminhtml_module';
        $this->_updateButton('save', 'label', Mage::helper('translation')->__('Save Module'));
        $this->_updateButton('delete', 'label', Mage::helper('translation')->__('Delete Module'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('translation')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getHeaderText() {
        if( Mage::registry('module_data') && Mage::registry('module_data')->getId() ) {
            return Mage::helper('translation')->__("Edit Module '%s'", $this->escapeHtml(Mage::registry('module_data')->getName()));
        }
        else {
            return Mage::helper('translation')->__('Add Module');
        }
    }
}
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
 * Module edit form
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Block_Adminhtml_Module_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare form
     * @access protected
     * @return Easylife_Translation_Block_Adminhtml_Module_Edit_Form
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                'id'         => 'edit_form',
                'action'     => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method'     => 'post',
                'enctype'    => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
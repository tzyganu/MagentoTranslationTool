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
 * Module edit form tab
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Block_Adminhtml_Module_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Translation_Module_Block_Adminhtml_Module_Edit_Tab_Form
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('module_');
        $form->setFieldNameSuffix('module');
        $this->setForm($form);
        $fieldset = $form->addFieldset('module_form', array('legend'=>Mage::helper('translation')->__('Module')));
        $tooltip = Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Module Name'), Mage::helper('translation')->__('This is the name of the module.<br />Example "Mage_Catalog", "Mage_Core", "Easylife_Translation"<br />Click on the autofill button to fill in automatically the rest of the fields. The autofill might not be accurate. Manual processing may be needed. Check the files field before submiting.'));
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('translation')->__('Module name'),
            'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',
            'after_element_html' =>$tooltip.'<button type="button" onclick="translationInstance.autofil()">'.Mage::helper('translation')->__('Try to autofill the rest of the fields').'</button>'
        ));

        $fieldset->addField('codepool', 'select', array(
            'label' => Mage::helper('translation')->__('Codepool'),
            'name'  => 'codepool',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> array(
                array(
                    'value' => 'core',
                    'label' => Mage::helper('translation')->__('Core'),
                ),
                array(
                    'value' => 'local',
                    'label' => Mage::helper('translation')->__('Local'),
                ),
                array(
                    'value' => 'community',
                    'label' => Mage::helper('translation')->__('Community'),
                ),
            ),
            'after_element_html' => Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Codepool'), Mage::helper('translation')->__('This is the codepool of the module. The folder in app/code where the module files are present.'))
        ));

        $fieldset->addField('alias', 'text', array(
            'label' => Mage::helper('translation')->__('Helper alias'),
            'name'  => 'alias',
            'required'  => true,
            'class' => 'required-entry',
            'after_element_html' => Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Helper alias'), Mage::helper('translation')->__('This is the main helper alias for the module.<br />Example: For module "Mage_Catalog" the helper alias is "catalog".<br />This is used for translating texts "Mage::helper(\'catalog\')->__(\'text here\')"'))
        ));

        $fieldset->addField('files', 'textarea', array(
            'label' => Mage::helper('translation')->__('Files'),
            'name'  => 'files',
            'required'  => true,
            'class' => 'required-entry',
            'note'=> Mage::helper('translation')->__('One on each line').Mage::helper('translation')->__('Prefix theme folders & files with *. For example "app/design/frontend/*/*/template/catalog/". This way the files will be searched in all the themes'),
            'after_element_html' => Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Files'), Mage::helper('translation')->__('List of files and folders to be scanned for translated texts. Add a trailing slash for folder names. Add one file or folder on one line.'))
        ));

        $fieldset->addField('exclude', 'textarea', array(
            'label' => Mage::helper('translation')->__('Exclude files'),
            'name'  => 'exclude',
            'note'    => $this->__('One on each line'),
            'after_element_html' => Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Files'), Mage::helper('translation')->__('List of files to be ignored by the scanning process. Add each on one line.'))
        ));

        if (Mage::getSingleton('adminhtml/session')->getModuleData()){
            $form->setValues(Mage::getSingleton('adminhtml/session')->getModuleData());
            Mage::getSingleton('adminhtml/session')->setModuleData(null);
        }
        elseif (Mage::registry('current_module')){
            $form->setValues(Mage::registry('current_module')->getData());
        }
        return parent::_prepareForm();
    }
}
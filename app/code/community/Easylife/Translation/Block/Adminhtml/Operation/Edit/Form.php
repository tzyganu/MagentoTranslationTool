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
class Easylife_Translation_Block_Adminhtml_Operation_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Translation_Module_Block_Adminhtml_Module_Edit_Tab_Form
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getUrl('adminhtml/translation_operation/save'), 'method' => 'post')
        );
        $form->setHtmlIdPrefix('operation_');
        $form->setFieldNameSuffix('operation');
        $this->setForm($form);
        $model = Mage::getSingleton('translation/operation');
        $fieldset = $form->addFieldset('operation_form', array('legend'=>Mage::helper('translation')->__('Operation')));
        $fieldset->addField('operation', 'select', array(
            'label'             => Mage::helper('translation')->__('Action'),
            'name'              => 'operation',
            'required'          => true,
            'class'             => 'required-entry',
            'values'            => $model->getAvailableOperations(true),
            'after_element_html'=> Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Operation'), $model->getOperationsDescriptionsHtml(), '500px')
        ));
        $fieldset->addField('locale', 'text', array(
            'label'=>Mage::helper('translation')->__('Locale Code'),
            'name'=>'locale',
            'after_element_html'=> Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Locale Code'), Mage::helper('translation')->__('Fill in the locale code to be used. The name of the folder in app/locale/'))
        ));
        $fieldset->addField('ignore', 'select', array(
            'name'      => 'ignore',
            'label'     => Mage::helper('translation')->__('Ignore helpers from other modules'),
            'values'    => array(
                array(
                    'label'=>Mage::helper('translation')->__('Yes'),
                    'value'=>1
                ),
                array(
                    'label'=>Mage::helper('translation')->__('No'),
                    'value'=>0
                )
            ),
            'after_element_html'=> Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Ignore helpers from other modules'), Mage::helper('translation')->__('If this is set to YES and a text in a module file is translated using an unknown helper alias, that text will be skipped.<br /> If it is set to NO, then all texts translated with an unknown helper will be added in a file with the same name as the helper alias.'))
        ));
        $fieldset->addField('use_any', 'select', array(
            'name'      => 'use_any',
            'label'     => Mage::helper('translation')->__('Use any module'),
            'values'    => array(
                array(
                    'label'=>Mage::helper('translation')->__('Yes'),
                    'value'=>1
                ),
                array(
                    'label'=>Mage::helper('translation')->__('No'),
                    'value'=>0
                )
            ),
            'after_element_html'=> Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Use any module'), Mage::helper('translation')->__('If this is set to YES and and a text translation is not found in the specified module then it will be searched in other module translation files.'))
        ));
        $fieldset->addField('themes', 'multiselect', array(
            'name'      => 'themes',
            'label'     => Mage::helper('translation')->__('Frontend Themes'),
            'values'    => Mage::getModel('core/design_source_design')->getAllOptions(false),
            'value'     => array('base/default', 'default/default'),
            'required'  => true,
            'class'     => 'required-entry',
            'after_element_html'=> Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Frontend Themes'), Mage::helper('translation')->__('Frontend themes that will be scanned for translatable texts.'))
        ));
        $grid = Mage::app()->getLayout()->createBlock('translation/adminhtml_module_grid')
            ->setUsedInActions(true)
            ->setInitialFilter(true)
            ->setNameInLayout('module_grid');
        $serializer = Mage::app()->getLayout()->createBlock('adminhtml/widget_grid_serializer')
            ->setName('module_grid_serializer');
        $serializer->initSerializerBlock('module_grid', 'getSelectedModules', 'custom_modules', 'custom_modules');
        $tooltip = Mage::helper('translation/adminhtml')->getTooltipHtml(Mage::helper('translation')->__('Modules'), Mage::helper('translation')->__('Select modules to be scanned. If All Available is selected then all the modules configured in <a href="%s">the module administration</a> section will be scanned', $this->getUrl('adminhtml/translation_module/index')));
        $fieldset->addField('modules', 'select', array(
            'name'      => 'modules',
            'label'     => Mage::helper('translation')->__('Modules'),
            'values'    => array(
                array(
                    'label'=>Mage::helper('translation')->__('All Available'),
                    'value'=>1
                ),
                array(
                    'label'=>Mage::helper('translation')->__('Custom'),
                    'value'=>0
                )
            ),
            'after_element_html'=> $tooltip.'<div id="module-grid">'.$grid->toHtml().$serializer->toHtml().'</div>',
            'value'=>1
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
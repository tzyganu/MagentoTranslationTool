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
 * Module admin grid block
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Block_Adminhtml_Module_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function __construct(){
        parent::__construct();
        $this->setId('moduleGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if (Mage::app()->getRequest()->getParam('used_in_actions')){
            $this->setUsedInActions(true);
        }
    }
    /**
     * prepare collection
     * @access protected
     * @return Easylife_Translation_Block_Adminhtml_Module_Grid
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('translation/module')->getCollection();
        if ($this->getInitialFilter()) {
            $collection->addFieldToFilter('entity_id', '-1');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare grid collection
     * @access protected
     * @return Easylife_Translation_Block_Adminhtml_Module_Grid
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareColumns() {
        if ($this->getUsedInActions()) {
            $this->addColumn('in_modules', array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_modules',
                'values'=> $this->_getSelectedModules(),
                'align' => 'center',
                'index' => 'entity_id'
            ));
        }
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('translation')->__('Id'),
            'index'        => 'entity_id',
            'type'        => 'number'
        ));
        $this->addColumn('name', array(
            'header'=> Mage::helper('translation')->__('Module name'),
            'index' => 'name',
            'type'         => 'text',
        ));
        $this->addColumn('codepool', array(
            'header'=> Mage::helper('translation')->__('Codepool'),
            'index' => 'codepool',
            'type'        => 'options',
            'options'=> array(
                'core' => Mage::helper('translation')->__('Core'),
                'local' => Mage::helper('translation')->__('Local'),
                'community'=>Mage::helper('translation')->__('Community'),
            ),
        ));
        if (!$this->getUsedInActions()){
            $this->addColumn('files', array(
                'header'=> Mage::helper('translation')->__('Files'),
                'index' => 'files',
                'type'  => 'text',
                'getter'=>'getFilesBr'
            ));
            $this->addColumn('action',
                array(
                    'header'=>  Mage::helper('translation')->__('Action'),
                    'width' => '100',
                    'type'  => 'action',
                    'getter'=> 'getId',
                    'actions'   => array(
                        array(
                            'caption'   => Mage::helper('translation')->__('Edit'),
                            'url'   => array('base'=> '*/*/edit'),
                            'field' => 'id'
                        )
                    ),
                    'filter'=> false,
                    'is_system'    => true,
                    'sortable'  => false,
            ));
            $this->addExportType('*/*/exportCsv', Mage::helper('translation')->__('CSV'));
            $this->addExportType('*/*/exportExcel', Mage::helper('translation')->__('Excel'));
            $this->addExportType('*/*/exportXml', Mage::helper('translation')->__('XML'));
        }
        return parent::_prepareColumns();
    }
    /**
     * prepare mass action
     * @access protected
     * @return Easylife_Translation_Block_Adminhtml_Module_Grid
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareMassaction() {
        if ($this->getUsedInActions()) {
            return $this;
        }
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('module');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('translation')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('translation')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('codepool', array(
            'label'=> Mage::helper('translation')->__('Change Codepool'),
            'url'  => $this->getUrl('*/*/massCodepool', array('_current'=>true)),
            'additional' => array(
                'flag_codepool' => array(
                        'name' => 'flag_codepool',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('translation')->__('Codepool'),
                        'values' => array(
                            'core' => Mage::helper('translation')->__('Core'),
                            'local' => Mage::helper('translation')->__('Local'),
                            'community'=>Mage::helper('translation')->__('Community'),
                        )
                )
            )
        ));
        return $this;
    }
    /**
     * get the row url
     * @access public
     * @param Easylife_Translation_Model_Module
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getRowUrl($row){
        if ($this->getUsedInActions()) {
            return '#';
        }
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    /**
     * get the grid url
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridUrl() {
        return $this->getUrl('*/translation_module/grid', array('_current'=>true, 'used_in_actions'=>$this->getUsedInActions()));
    }
    /**
     * after collection load
     * @access protected
     * @return Easylife_Translation_Block_Adminhtml_Module_Grid
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _afterLoadCollection() {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * get selected modules
     * @access protected
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getSelectedModules() {
        return $this->getRequest()->getPost('modules');
    }

    /**
     * filter collection
     * @access protected
     * @param $column
     * @return Easylife_Translation_Block_Adminhtml_Module_Grid
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_modules') {
            $moduleIds = $this->_getSelectedModules();
            if (empty($moduleIds)) {
                $moduleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$moduleIds));
            }
            else {
                if($moduleIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$moduleIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
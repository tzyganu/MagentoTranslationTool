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
 * Module admin controller
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Adminhtml_Translation_ModuleController extends Mage_Adminhtml_Controller_Action {
    /**
     * init the module
     * @access protected
     * @return Easylife_Translation_Model_Module
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _initModule(){
        $moduleId  = (int) $this->getRequest()->getParam('id');
        $module    = Mage::getModel('translation/module');
        if ($moduleId) {
            $module->load($moduleId);
        }
        Mage::register('current_module', $module);
        return $module;
    }
     /**
     * default action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('translation')->__('Translation'))
             ->_title(Mage::helper('translation')->__('Modules'));
        $this->renderLayout();
    }
    /**
     * grid action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function gridAction() {
        $this->loadLayout()->renderLayout();
    }
    /**
     * edit module - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function editAction() {
        $moduleId    = $this->getRequest()->getParam('id');
        $module      = $this->_initModule();
        if ($moduleId && !$module->getId()) {
            $this->_getSession()->addError(Mage::helper('translation')->__('This module no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $module->setData($data);
        }
        Mage::register('module_data', $module);
        $this->loadLayout();
        $this->_title(Mage::helper('translation')->__('Translation'))
             ->_title(Mage::helper('translation')->__('Modules'));
        if ($module->getId()) {
            $this->_title($module->getName());
        }
        else {
            $this->_title(Mage::helper('translation')->__('Add module'));
        }
        $this->renderLayout();
    }
    /**
     * new module action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * save module - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('module')) {
            try {
                $module = $this->_initModule();
                $module->addData($data);
                $module->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translation')->__('Module was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $module->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('There was a problem saving the module.'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Unable to find module to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete module - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $module = Mage::getModel('translation/module');
                $module->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translation')->__('Module was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('There was an error deleteing module.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Could not find module to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete module - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function massDeleteAction() {
        $moduleIds = $this->getRequest()->getParam('module');
        if(!is_array($moduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Please select modules to delete.'));
        }
        else {
            try {
                foreach ($moduleIds as $moduleId) {
                    $module = Mage::getModel('translation/module');
                    $module->setId($moduleId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translation')->__('Total of %d modules were successfully deleted.', count($moduleIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('There was an error deleteing modules.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass codepool change - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function massCodepoolAction() {
        $moduleIds = $this->getRequest()->getParam('module');
        if(!is_array($moduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Please select modules.'));
        }
        else {
            try {
                foreach ($moduleIds as $moduleId) {
                    $module = Mage::getSingleton('translation/module')->load($moduleId)
                            ->setCodepool($this->getRequest()->getParam('flag_codepool'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d modules were successfully updated.', count($moduleIds)));
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('There was an error updating modules.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * autofil action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function autofillAction() {
        $moduleName = $this->getRequest()->getParam('module');
        $node = Mage::getConfig()->getNode('modules/'.$moduleName);
        $response = array();
        $response['error'] = 0;
        if (!$node) {
            $response['error'] = 1;
            $response['message'] = Mage::helper('core')->escapeHtml(Mage::helper('translation')->__('Module %s not found', $moduleName));
        }
        else {
            $modulePath = str_replace('_', DS, $moduleName);
            $codepool = (string)$node->codePool;
            $configFile = 'app'.DS.'code'.DS.$codepool.DS.$modulePath.DS.'etc'.DS.'config.xml';
            $xmlContent = file_get_contents($configFile);
            $xmlData = new SimpleXMLElement($xmlContent);
            $helper = Mage::helper('translation')->getHelperAlias($moduleName, $xmlData);
            $files = Mage::helper('translation')->getModuleFiles($moduleName, $xmlData, $codepool);
            $response['codepool'] = $codepool;
            $response['helper'] = $helper;
            $response['files'] = $files;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * export as csv - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function exportCsvAction(){
        $fileName   = 'module.csv';
        $content    = $this->getLayout()->createBlock('translation/adminhtml_module_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function exportExcelAction(){
        $fileName   = 'module.xls';
        $content    = $this->getLayout()->createBlock('translation/adminhtml_module_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function exportXmlAction(){
        $fileName   = 'module.xml';
        $content    = $this->getLayout()->createBlock('translation/adminhtml_module_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}
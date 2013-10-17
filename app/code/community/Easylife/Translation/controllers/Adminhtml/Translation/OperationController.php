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
 * Operations admin controller
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Adminhtml_Translation_OperationController extends Mage_Adminhtml_Controller_Action {
     /**
     * default action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function indexAction() {
        $this->_forward('edit');
    }
    /**
     * new operation action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * edit operation - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function editAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('translation')->__('Translation'))
             ->_title(Mage::helper('translation')->__('Operations'));
        $this->renderLayout();
    }
    /**
     * save module - action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function saveAction() {
        $action = $this->getRequest()->getPost('operation');
        if (!isset($action['operation'])) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Operation does not exist'));
        }
        $operations = Mage::getSingleton('translation/operation')->getOperationAsOptions();
        if (!isset($operations[$action['operation']])) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Operation does not exist'));
        }
        else {
            $operationConfig = $operations[$action['operation']];
            if (!isset($operationConfig['handler'])) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Operation %s does not have a handler', $operationConfig['label']));
            }
            else {
                $handler = $operationConfig['handler'];
                $parts = explode('::', $handler);
                if (count($parts) != 2) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Handler %s for operation %s configured wrong', $handler, $operationConfig['label']));
                }
                else{
                    $handlerModel = Mage::getModel($parts[0]);
                    $method = $parts[1];
                    if (!method_exists($handlerModel, $method)) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translation')->__('Handler %s for operation %s cannot be called', $handler, $operationConfig['label']));
                    }
                    else {
                        $data = array();
                        $data['operation'] = $action['operation'];
                        foreach (array_keys($operationConfig['fields']) as $key) {
                            if (isset($action[$key])) {
                                $data[$key] = $action[$key];
                            }
                            if ($key == 'modules') {
                                $data['custom_modules'] = $this->getRequest()->getPost('custom_modules');
                            }
                        }
                        try{
                            $response = $handlerModel->$method($data);
                            $this->_prepareDownloadResponse(basename($response), array(
                                'type'=>'filename',
                                'value'=>$response,
                                'rm'=>true
                            ));
                        }
                        catch (Exception $e) {
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        }
                    }
                }
            }
        }
        $this->_redirect('*/*/');
    }
}
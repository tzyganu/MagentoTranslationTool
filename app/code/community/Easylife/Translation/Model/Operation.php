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
 * Operation source model
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Model_Operation extends Varien_Object{
    /**
     * available operations
     * @var null|array
     */
    protected $_availableOperations = null;
    /**
     * config path to available operations
     */
    const XML_OPERATIONS_CONFIG_PATH = 'global/operations';

    /**
     * get the available operations
     * @access public
     * @param bool $withEmpty
     * @return array|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAvailableOperations($withEmpty = false){
        if (is_null($this->_availableOperations)){
            $operations = Mage::getConfig()->getNode(self::XML_OPERATIONS_CONFIG_PATH);
            foreach ((array)$operations as $key=>$value){
                $this->_availableOperations[] = array(
                    'label'=>(string)$value->label,
                    'value'=>$key,
                    'fields'=>(array)$value->show_fields,
                    'handler'=>(string)$value->handler,
                    'description'=>(string)$value->description
                );
            }
        }
        $op = $this->_availableOperations;
        if ($withEmpty){
            array_unshift($op, array(
                'label'=>'',
                'value'=>''
            ));
        }
        return $op;
    }

    /**
     * get operations as array options
     * @access public
     * @param bool $withEmpty
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getOperationAsOptions($withEmpty = false){
        $options = $this->getAvailableOperations($withEmpty);
        $array = array();
        foreach($options as $option){
            $array[$option['value']] = $option;
        }
        return $array;
    }

    /**
     * get operation descriptions
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getOperationsDescriptionsHtml() {
        $operations = $this->getAvailableOperations();
        $html = '<h2><b>'.Mage::helper('translation')->__('Available operations').'</b></h2><br />';
        foreach ($operations as $operation) {
            $html .= '<b>'.$operation['label'].'</b><br />';
            $html .= '<p>'.$operation['description'].'</p><br />';
        }
        return $html;
    }
}
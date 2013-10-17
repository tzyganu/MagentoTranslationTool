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
 * Module resource model
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Translation_Model_Resource_Module extends Mage_Core_Model_Resource_Db_Abstract {
    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function _construct() {
        $this->_init('translation/module', 'entity_id');
    }
}
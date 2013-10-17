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
 * Translation module upgrade script
 *
 * @category    Easylife
 * @package     Easylife_Translation
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
$this->startSetup();
$this->getConnection()
    ->addColumn($this->getTable('translation/module'), 'exclude', array(
        'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'LENGTH'    => '64k',
        'NULLABLE'  => true,
        'COMMENT'   => 'Excluded files'
    ));
$this->endSetup();
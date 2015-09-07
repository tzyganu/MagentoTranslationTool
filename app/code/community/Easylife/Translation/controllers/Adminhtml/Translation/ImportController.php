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
 * @author      Sander Mangel <sander@sandermangel.nl>
 */
class Easylife_Translation_Adminhtml_Translation_ImportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Import modules with modman
     * @access public
     * @return void
     * @author Sander Mangel <sander@sandermangel.nl>
     */
    public function modmanAction()
    {
        $modmanDir = Mage::getBaseDir() . DS . '.modman' . DS;

        foreach ((array)scandir($modmanDir) as $module) {
            if (in_array($module, array('.','..'))) continue;

            /**
             * Get all module XMLs from the app/etc/modules
             */
            $modules = array();
            foreach ((array)glob($modmanDir . $module . DS . 'app' . DS . 'etc' . DS . 'modules' . DS . '*.xml') as $moduleXml) {
                try {
                    $sxe = new SimpleXMLElement(file_get_contents($moduleXml));
                    foreach ($sxe->xpath('/config/modules')[0]->children() as $child)
                    {
                        $modules[str_replace('_', '/', $child[0]->getName())] = array(
                            'name' => $child[0]->getName(),
                            'codePool' => "{$child[0]->codePool[0]}",
                            'helper' => 'core'
                        );
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('admin/session')->addWarning("Module '{$module}', ".basename($moduleXml)." is not valid XML");
                    continue;
                }
            }

            /**
             * We'll take a look at the modman file
             * to determine which file paths should be included
             */
            $files = array();
            if ($h = fopen($modmanDir . $module . DS . 'modman', 'r')) {
                while (($content = fgets($h)) !== false) {
                    if (strstr($content, '#')) continue; // comment, ignore
                    $lineParts = preg_split('/[\s\t]+/', trim($content)); // split on whitespace & tab
                    $destination = end($lineParts);

                    // only files in app/design & app/code
                    if (!strstr($destination, 'app/code') && !strstr($destination, 'app/design')) continue;
                    // check for translation files in template dir
                    if (strstr($destination, 'csv')) continue;

                    $files[] = preg_replace('/(adminhtml|frontend)\/([^\/]+)\/([^\/]+)/', '$1/*/*', $destination);

                    /**
                     * We'll try to find the helper if this is the codepool path
                     */
                    if (strstr($destination, 'app/code')) {
                        foreach ($modules as $path => $m) { // go through the found modules from app/etc/modules
                            if (strstr($destination, $path)) { // does this path belong to it?
                                // if so get helper namespace from config.xml
                                try {
                                    $sxe = new SimpleXMLElement(file_get_contents($destination . DS . 'etc' . DS . 'config.xml'));
                                    foreach ($sxe->xpath('/config/global/helpers')[0]->children() as $child)
                                    {
                                        $modules[$path]['helper'] = $child[0]->getName();
                                    }
                                } catch (Exception $e) {
                                    Mage::getSingleton('admin/session')->addWarning("No valid config.xml found for module '{$module}'");
                                    continue;
                                }
                            }
                        }
                    }
                }
            } else {
                Mage::getSingleton('admin/session')->addWarning("Module '{$module}' has no accessible modman file");
                continue;
            }

            foreach ($modules as $properties) {
                $_module = Mage::getModel('translation/module')->load($properties['name'], 'name'); // check if it already exists
                if ($_module->getId()) continue;

                $_module = Mage::getModel('translation/module');
                $_module->addData(array(
                    'name'      => $properties['name'],
                    'codepool'  => $properties['codePool'],
                    'alias'     => $properties['helper'],
                    'files'     => implode("\r\n", $files)
                ));

                try {
                    $_module->save();
                } catch (Exception $e) {
                    Mage::getSingleton('admin/session')->addError("Error while savind {$properties['name']}");
                    continue;
                }
            }
        }

        $this->_redirectReferer();
    }
}

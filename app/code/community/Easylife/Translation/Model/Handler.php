<?php
class Easylife_Translation_Model_Handler extends Varien_Object {
    /**
     * @var list of helpers
     */
    protected $_helpers = null;
    /**
     * @var thelems to parse
     */
    protected $_themes = array();
    /**
     * @var cache for parsed files
     */
    protected $_processedFiles = array();
    /**
     * @var ignore helpers outside the module
     */
    protected $_ignoreHelpers = false;
    /**
     * pattern for helper('alias')->__ ('Text')
     */
    const HELPER_PATTERN = '/helper\(\\\'([a-z_]+)\\\'\)-\>__\([\s]*([\'|\\\"])(.*?[^\\\\])\\2.*?\)/';
    /**
     * pattern for $this->__ ('Text')
     */
    const THIS_PATTERN = '/\$this-\>__\([\s]*([\'|\\\"])(.*?[^\\\\])\\1.*?\)/';
    /**
     * pattern for __ ('text')
     */
    const UNDERSCORE_PATTERN = '/[^-][^>]__\([\s]*([\'|\\\"])(.*?[^\\\\])\\1.*?\)/';
    /**
     * @var file extensions to parse
     */
    protected $_allowedExtension = array(
        'xml'=>'xml',
        'php'=>'php',
        'phtml'=>'php'
    );
    /**
     * files that shouldn't be parsed
     * @var array()
     */
    protected $_restrictedFiles = array('wsdl.xml', 'wsi.xml', 'wsdl2.xml');

    /**
     * restructed folders
     */
    protected $_restrictedFolders = array('.svn');

    /**
     * file handler
     * @var null
     */
    protected $_io = null;
    /**
     * collect texts with the key=value
     * @access public
     * @param $params
     * @return string
     * @throws Easylife_Translation_Exception
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getUntranslated($params) {
        if (!isset($params['locale'])) {
            throw new Easylife_Translation_Exception(Mage::helper('translation')->__('Locale not set'));
        }
        $locale = $params['locale'];
        $sourceFiles = glob('app'.'/'.'locale'.'/'.$locale.'/' .'*.csv');
        if (count($sourceFiles) == 0) {
            throw new Easylife_Translation_Exception(Mage::helper('translation')->__('No file in the locale folder %s', $locale));
        }
        $texts = array();
        $io = $this->getIo();
        foreach ($sourceFiles as $file) {
            $base = basename($file);
            $io->streamOpen($file, 'r+');
            while($data = $io->streamReadCsv()) {
                if (isset($data[0]) && isset($data[1]) && $data[0] == $data[1]) {
                    $texts[$base][] = array($data[0], $data[1]);
                }
            }
        }
        $tempFolder = Mage::helper('translation')->getTempDir().md5(microtime()).'/';
        $io->checkAndCreateFolder($tempFolder);
        $io->cd($tempFolder);
        foreach ($texts as $fileName=>$data){
            $files[] = $fileName;
            $io->streamOpen($fileName, 'w');
            foreach ($data as $text){
                $io->streamWriteCsv($text);
            }
            $io->streamClose();
        }
        $this->_createArchive($files, $locale);
        $io->cd(Mage::helper('translation')->getTempDir());
        $io->rmdir($tempFolder, true);
        return Mage::helper('translation')->getTempDir().$locale.'.tgz';
    }

    /**
     * collect texts to translate
     * @access public
     * @param $params
     * @return string
     * @throws Easylife_Translation_Exception
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function collectTexts($params) {
        if (!isset($params['modules']) && !isset($params['custom_modules'])) {
            throw new Easylife_Translation_Exception(Mage::helper('translation')->__('No modules specified'));
        }
        if ($params['modules'] == 0) {
            if (!isset($params['custom_modules'])) {
                throw new Easylife_Translation_Exception(Mage::helper('translation')->__('No modules specified'));
            }
            $moduleIds = explode('&', $params['custom_modules']);
        }
        else {
            $moduleIds = Mage::getModel('translation/module')->getCollection()->getAllIds();
        }
        $this->setIgnoreHelpers($params['ignore']);
        $themes = isset($params['themes']) ? $params['themes'] : array();
        $fileContents = $this->getModuleTexts($moduleIds, $themes);

        $io = $this->getIo();
        $tempFolder = Mage::helper('translation')->getTempDir().md5(microtime()).'/';
        $io->checkAndCreateFolder($tempFolder);
        $io->cd($tempFolder);
        foreach ($fileContents as $moduleName=>$texts) {
            ksort($texts);
            $files[] = $moduleName.'.csv';
            $io->streamOpen($moduleName.'.csv', 'w');
            foreach ($texts as $text) {
                $arr = array($text, $text);
                $io->streamWriteCsv($arr);
            }
            $io->streamClose();
        }
        $this->_createArchive($files, 'collected_texts');
        $io->cd(Mage::helper('translation')->getTempDir());
        $io->rmdir($tempFolder, true);
        return Mage::helper('translation')->getTempDir().'collected_texts.tgz';
    }

    /**
     * generate files for locale
     * @access public
     * @param $params
     * @return string
     * @throws Easylife_Translation_Exception
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function generateLocale($params) {
        if (!isset($params['modules']) && !isset($params['custom_modules'])) {
            throw new Easylife_Translation_Exception(Mage::helper('translation')->__('No modules specified'));
        }
        if (!isset($params['locale'])) {
            throw new Easylife_Translation_Exception(Mage::helper('translation')->__('Locale not set'));
        }
        if ($params['modules'] == 0) {
            if (!isset($params['custom_modules'])) {
                throw new Easylife_Translation_Exception(Mage::helper('translation')->__('No modules specified'));
            }
            $moduleIds = explode('&', $params['custom_modules']);
        }
        else {
            $moduleIds = Mage::getModel('translation/module')->getCollection()->getAllIds();
        }
        $this->setIgnoreHelpers($params['ignore']);
        $themes = isset($params['themes']) ? $params['themes'] : array();
        $fileContents = $this->getModuleTexts($moduleIds, $themes);

        $io = $this->getIo();
        $tempFolder = Mage::helper('translation')->getTempDir().md5(microtime()).'/';
        $io->checkAndCreateFolder($tempFolder);
        //read existing modules
        $existingTranslations = $this->getExistingTranslations($params['locale']);
        $byModule = $existingTranslations['by_module'];
        $byText = $existingTranslations['by_text'];
        //$translated = array();
        $io->cd($tempFolder);
        foreach ($fileContents as $moduleName=>$texts) {
            ksort($texts);
            $files[] = $moduleName.'.csv';
            $io->streamOpen($moduleName.'.csv', 'w');
            foreach ($texts as $text) {
                //check if text is translated in module
                $arr = array();
                $arr[] = $text;
                if (isset($byModule[$moduleName][$text])) {
                    $arr[] = $byModule[$moduleName][$text];
                    $arr[] = 'true';
                }
                elseif ($params['use_any'] && isset($byText[$text])) {
                    $values = array_values($byText[$text]);
                    $arr[] = $values[0];
                    $arr[] = 'true';
                }
                else {
                    $arr[] = $text;
                    $arr[] = 'false';
                }
                $io->streamWriteCsv($arr);
            }
            $io->streamClose();
        }
        $this->_createArchive($files, $params['locale']);
        $io->cd(Mage::helper('translation')->getTempDir());
        $io->rmdir($tempFolder, true);
        return Mage::helper('translation')->getTempDir().$params['locale'].'.tgz';
    }
    /**
     * retrieve texts to translate
     * @access public
     * @param $moduleIds
     * @param mixed $themes
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getModuleTexts($moduleIds, $themes) {
        $this->_processedFiles = array();
        $modules = Mage::getModel('translation/module')->getCollection()
            ->addFieldToFilter('entity_id', array('in'=>$moduleIds));
        foreach ($modules as $module) {
            $this->_helpers[$module->getAlias()] = $module->getName();
        }
        $texts = array();
        foreach ($modules as $module) {
            foreach ($module->getFiles(true) as $file) {
                $files = $this->_getFilesInThemes($file, $themes);
                foreach ($files as $_file) {
                    $realFiles = glob(trim($_file));
                    foreach ($realFiles as $realFile) {
                        if (!in_array($realFile, $module->getExcludedFiles(true))) {
                            $texts = $this->mergeTexts($texts, $this->getFileTexts($realFile, $module->getName()));
                        }
                    }
                }
            }
        }
        return $texts;
    }


    /**
     * get texts from a file/folder
     * @access public
     * @param $file
     * @param $module
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getFileTexts($file, $module) {
        $file = trim($file);
        if (is_dir($file)) {
            $texts = array();
            if (in_array(basename($file), $this->_restrictedFolders)) {
                return $texts;
            }
            if ($handle = opendir($file)) {
                while ($f = readdir($handle)) {
                    if ($f == '.' || $f == '..') {
                        continue;
                    }
                    if (substr($file, -1) == '/') {
                        $nextFile = $file.$f;
                    }
                    else{
                        $nextFile = $file.'/'.$f;
                    }
                    $texts = $this->mergeTexts($texts, $this->getFileTexts($nextFile, $module));
                    unset($f);
                }
                closedir($handle);
            }
            return $texts;
        }
        else {
            if (in_array(basename($file), $this->_restrictedFiles)) {
                return array();
            }
            if (isset($this->_processedFiles[$file])) {
                return array();
            }
            $parts = explode('.', $file);
            if (count($parts) < 2) {
                return array();
            }
            $extension = $parts[count($parts) - 1];
            if (!isset($this->_allowedExtension[$extension])) {
                return array();
            }
            switch($this->_allowedExtension[$extension]) {
                case 'php':
                    $this->_processedFiles[$file] = 1;
                    return $this->parsePhpFile($file, $module);
                case 'xml':
                    $this->_processedFiles[$file] = 1;
                    return $this->parseXmlFile($file, $module);
                default:
                    $this->_processedFiles[$file] = 1;
                    return array();
            }
        }
    }

    /**
     * parse a php file for texts
     * @access public
     * @param $file
     * @param $module
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function parsePhpFile($file, $module){
        $texts = array();
        $helpers = $this->_helpers;
        foreach (file($file) as $number => $line) {
            $matches = array();
            if (preg_match_all(self::HELPER_PATTERN, $line, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $k => $match) {
                    if (!isset($helpers[$match[1]])) {
                        if ($this->getIgnoreHelpers()){
                            continue;
                        }
                        $translationKey = $match[3];
                        $texts[$match[1]][$translationKey] = $translationKey;
                        continue;
                    }
                    $moduleName     = $helpers[$match[1]];
                    $translationKey = $match[3];
                    $texts[$moduleName][$translationKey] = $translationKey;
                }
            }
            if (preg_match_all(self::THIS_PATTERN, $line, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $k => $match) {
                    $moduleName     = $module;
                    $translationKey = $match[2];
                    $texts[$moduleName][$translationKey] = $translationKey;
                }
            }
            if (preg_match_all(self::UNDERSCORE_PATTERN, $line, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $k => $match) {
                    $moduleName     = 'translate';
                    $translationKey = $match[2];
                    $texts[$moduleName][$translationKey] = $translationKey;
                }
            }
        }
        return $texts;
    }
    /**
     * parse an XML file for texts
     * @access public
     * @param $file
     * @param $module
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function parseXmlFile($file, $module){
        $texts = array();
        $xmlContent = file_get_contents($file);
        try{
            $xmlData = new SimpleXMLElement($xmlContent);
        }
        catch (Exception $e){
            //do nothing if the xml is not formatted correctly
        }
        $xmlTranslate = $this->getXmlTranslates($xmlData, $module);
        $helpers = $this->_helpers;
        foreach ($xmlTranslate as $translate) {
            if (isset($helpers[$translate['module']])) {
                $moduleName = $helpers[$translate['module']];
            }
            else{
                $moduleName = $translate['module'];
            }
            $translationKey = $translate['value'];
            $texts[$moduleName][$translationKey] = $translationKey;
        }
        return $texts;
    }

    /**
     * go deep in an xml and get the translatable texts
     * @access public
     * @param $xmlNode
     * @param null $module
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getXmlTranslates($xmlNode, $module = null){
        if (is_null($module)) {
            $module = $module->getAlias();
        }
        $translate = array();
        foreach ($xmlNode as $node) {
            $attributes = $node->attributes();
            if (isset($attributes['translate'])) {
                $module = isset($attributes['module']) ? (string)$attributes['module'] : $module;
                $translateNodes = explode(' ', $attributes['translate']);

                foreach ($translateNodes as $nodeName) {
                    if (!(string)$node->$nodeName) {
                        continue;
                    }
                    if (!$this->getIgnoreHelpers() || in_array($module, $this->_helpers)){
                        $translate[] = array(
                            'module'    => $module,
                            'value'     => (string)$node->$nodeName,
                        );
                    }
                }
            }
            $translate = array_merge($translate, $this->getXmlTranslates($node, $module));
        }
        return $translate;
    }

    /**
     * merge collected texts
     * @access public
     * @param $arr1
     * @param $arr2
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function mergeTexts($arr1, $arr2){
        return Mage::helper('translation')->mergeTexts($arr1, $arr2);
    }

    /**
     * get file handler
     * @access public
     * @return null|Varien_Io_File
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getIo(){
        if (is_null($this->_io)){
            $this->_io = new Varien_Io_File();
        }
        return $this->_io;
    }

    /**
     * get frontend files from specified themes
     * @access protected
     * @param $file
     * @param $themes
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getFilesInThemes($file, $themes){
        $toScan = array();
        if (count($themes) == 0) {
            return array($file);
        }
        if (substr($file, 0, strlen('app/design/frontend')) == 'app/design/frontend') {
            foreach ($themes as $theme) {
                $toScan[] = str_replace('*/*', $theme, $file);
            }
            return $toScan;
        }
        return array($file);
    }

    /**
     * parse the locale folder and get the existing translations
     * @access public
     * @param $locale
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getExistingTranslations($locale) {
        $io = $this->getIo();
        $folder = 'app/locale/'.$locale.'/';
        $texts = array();
        $texts['by_module'] = array();
        $texts['by_text'] = array();
        try {
            if ($handle = opendir($folder)) {
                while ($f = readdir($handle)) {
                    if (is_file($folder.$f)){
                        $io->streamOpen($folder.$f, 'r+');
                        $module = str_replace('.csv', '', $f);
                        $texts['by_module'][$module] = array();
                        while($data = $io->streamReadCsv()){
                            if (isset($data[0]) && isset($data[1])){
                                $texts['by_module'][$module][$data[0]] = $data[1];
                                $texts['by_text'][$data[0]][$module] = $data[1];
                            }
                        }
                    }
                    unset($f);
                }
                closedir($handle);
            }

        }
        catch(Exception $e){
            return array('by_module'=>array(), 'by_text'=>array());
        }
        return $texts;
    }

    /**
     * archive a list of files
     * @access public
     * @param $files
     * @param $archiveName
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _createArchive($files, $archiveName){
        $_writer = Mage::getModel('translation/writer', $files);
        $_writer->setNamePackage('../'.$archiveName);
        $_writer->composePackage()->archivePackage();
        return Mage::helper('translation')->getTempDir().$archiveName.'.tgz';
    }
}
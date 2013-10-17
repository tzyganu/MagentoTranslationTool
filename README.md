Easylife Translation Tool for Magento.
======================
This Magento extension can be used to identify the translatable texts from magento files and collect them in csv files.
It's useful when you create an extension and you want to add the language file.
Also it can be used when you need to translate your website and the language pack from magentocommerce.com is for that language is not reliable.

Requirements
---------------
Magento CE 1.7.0.2. Most probably it works on other versions. I just didn't test.  
The *var* folder must be writable, but anyway you need that for Magento to work.  
All files must have read permissions. You need that also for Magento to work.  

What it does
----------------
For now it has 3 features.

 - collect texts from specified modules and generates a csv file for each module.
 - identifies the texts from a specified locale that have the same translation as the key: "Customer","Customer". *Note*: False positives may occur.
 - generates all the locale files for the selected modules in a specific language, by collecting the texts from the module files and merging with the already existing locale files.
 
Any other feature requests are welcomed.

How to use
---------------
After installation you can access the extension from *System->Tools->Translation*.  
Under that menu there are 2 menu items:  
 1. **Modules** - You can manage the magento modules to be parsed later. The extension comes with the core modules already configured. To add a new module click on *Add module* and fill in the following fields.  
    - Name: This is the module name: Ex: 'Easylife_Articles'.  
    - Codepool: The folder from `app/code` where the module is present  
    - Helper alias: The alias for the helper of the module used for translation: `Mage::helper('articles')->__('Title')`.  
    - Files: This is the list of files and folders to be parsed for the module.  
    - Excluded files: Files to be excluded when parsing for translatable texts.  
    After filling in the module name you can click on 'Autofill the rest of the fields' and the extension will try to detect the rest of the values.For the "Files" field you may get unconsistent results (this feature is still experimental), so check them before saving.  
 2. **Operations** - from this screen you can do the operations described in the *What it does* section.

Limitations
-------------
While collecting the texts only and generating the translation files only the existing locale files are taken into consideration. The translations from the database and theme specific translation files are ignored.

Known issues
-------------
Magento allows you to specify in a block or controller if an other module is used for translation by adding this method:  
<pre><code>
    public function getModuleName(){
        return 'Mage_Catalog';
    }
</code></pre>
At this point these methods are ignored and the texts from the block or controller are associated to the original module and not the one specified in `getModuleName()`.

Uninstall
-------------
To uninstall the extension remove the following files and folders:
 - app/code/community/Easylife/Translation/
 - app/design/adminhtml/default/default/layout/easylife_translation.xml
 - app/design/adminhtml/default/default/template/easylife_translation/
 - app/etc/modules/Easylife_Translation.xml
 - var/translation/
After removing the files run these queries on the database (add table prefix if you have one)
 - `DROP TABLE translation_module;`
 - `DELETE FROM core_resource where code = 'easylife_translation_setup'`
Clear the cache when you are done.

Others
--------------
This extension does not rewrite any core classes.
The extension is still under tests. Please report any issues here: https://github.com/tzyganu/MagentoTranslationTool/issues
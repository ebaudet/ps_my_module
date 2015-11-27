<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from 202 ecommerce
 * Use, copy, modification or distribution of this source file without written
 * license agreement from 202 ecommerce is strictly forbidden.
 *
 * @author    Emilien Baudet <ebaudet@202-ecommerce.com>
 * @copyright Copyright (c) 202 ecommerce 2015
 * @license   Commercial license
 *
 * Support <support@202-ecommerce.com>
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class MyModule extends Module
{
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Emilien Baudet';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.4', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->includeFiles();

        $this->displayName = $this->l('My module');
        $this->description = $this->l('Description of my module');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5') {
            require(_PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward.php');
        }

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    /**
     * Include Files
     */
    private function includeFiles()
    {
        $path = $this->getLocalPath() . 'classes/';
        foreach (scandir($path) as $class) {
            if ($class != "index.php" && is_file($path . $class)) {
                $class_name = substr($class, 0, -4);
                if ($class_name != 'index' && !class_exists($class_name)) {
                    require_once($path . $class_name . '.php');
                }
            }
        }

        $path .= 'helper/';

        foreach (scandir($path) as $class) {
            if ($class != "index.php" && is_file($path . $class)) {
                $class_name = substr($class, 0, -4);
                if ($class_name != 'index' && !class_exists($class_name)) {
                    require_once($path . $class_name . '.php');
                }
            }
        }
    }

    ############################################################################################################
    # Install / Upgrade / Uninstall
    ############################################################################################################

    /**
     * Installing the module
     * @version 1.0.0
     * @return bool
     * @throws PrestaShopException
     */
    public function install()
    {
        if (_PS_VERSION_ >= '1.5') {
            if (Shop::isFeatureActive()) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
        }

        if (!$this->installTAbs()) {
            return false;
        }

        return $this->installSQL() &&
        parent::install() &&
        $this->registerHook('leftColumn') &&
        $this->registerHook('header') &&
        $this->registerHook('actionProductUpdate') &&
        $this->registerHook('displayAdminCatalog') &&
        Configuration::updateValue('MYMODULE_NAME', 'my friend');
    }

    /**
     * Removing the module
     * @version 1.0.0
     * @return bool
     */
    public function uninstall()
    {
        if (!$this->uninstallTabs()) {
            return false;
        }

        if (!$this->uninstallSQL()) {
            return false;
        }

        if (!Configuration::deleteByName('MYMODULE_NAME')) {
            return false;
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    ############################################################################################################
    # Tabs
    ############################################################################################################

    /**
     * Install Tabs
     * @return bool
     */
    public function installTAbs()
    {
        $menu_id = Tab::getIdFromClassName('AdminCatalog');

        // Install All Tabs directly via controller's install function
        $controllers = scandir($this->getLocalPath() . '/controllers/admin');
        foreach ($controllers as $controller) {
            if ($controller != 'index.php' && is_file($this->getLocalPath() . '/controllers/admin/' . $controller)) {
                require_once($this->getLocalPath() . '/controllers/admin/' . $controller);
                $controller_name = substr($controller, 0, -4);
                //Check if class_name is an existing Class or not
                if (class_exists($controller_name)) {
                    if (method_exists($controller_name, 'install')) {
                        if (!call_user_func(array($controller_name, 'install'), $menu_id, $this->name)) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Uninstall Tabs
     * @return bool
     */
    public function uninstallTabs()
    {
        return MAC_TotAdminTabHelper::deleteAdminTabs($this->name);
    }

    ############################################################################################################
    # SQL
    ############################################################################################################

    /**
     * Install SQL
     * @return bool
     */
    public function installSQL()
    {
        // Install All Object Model SQL via install function
        $classes = scandir($this->getLocalPath() . '/classes');
        foreach ($classes as $class) {
            if ($class != 'index.php' && is_file($this->getLocalPath() . '/classes/' . $class)) {
                $class_name = substr($class, 0, -4);
                // Check if class_name is an existing Class or not
                if (class_exists($class_name)) {
                    if (method_exists($class_name, 'install')) {
                        if (!call_user_func(array($class_name, 'install'))) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function uninstallSQL()
    {
        // Uninstall All Object Model SQL via install function
        $classes = scandir($this->getLocalPath() . '/classes');
        foreach ($classes as $class) {
            if ($class != 'index.php' && is_file($this->getLocalPath() . '/classes/' . $class)) {
                $class_name = substr($class, 0, -4);
                // Check if class_name is an existing Class or not
                if (class_exists($class_name)) {
                    if (method_exists($class_name, 'uninstall')) {
                        if (!call_user_func(array($class_name, 'uninstall'))) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    ############################################################################################################
    # Hook Display
    ############################################################################################################

    public function hookDisplayLeftColumn($params)
    {
        return $this->hookLeftColumn($params);
    }

    public function hookLeftColumn($params)
    {
        $this->myModuleLogError("methode hookLeftColumn appelée");
        if (_PS_VERSION_ < '1.5') {
            $my_module_link = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' . $this->name .
                '/sendtoMymodule.php?display=true&token=' . Tools::getAdminTokenLite('AdminModules');
        } else {
            $my_module_link = $this->context->link->getModuleLink('mymodule', 'display');
        }

        $this->context->smarty->assign(
            array(
                'my_module_name'    => Configuration::get('MYMODULE_NAME'),
                'my_module_link'    => $my_module_link,
                'my_module_message' => $this->l('This is a simple text message')
            )
        );

        return $this->display(__FILE__, '/views/templates/hook/mymodule.tpl');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/mymodule.css', 'all');
    }

    ############################################################################################################
    # Hook admin
    ############################################################################################################

    /**
     * Display Tab in BO
     * @return string
     */
    public function hookDisplayAdminCatalogExtra()
    {
        $data = array(
            'name'             => $this->displayName,
            'mymodule_product' => MyModuleProduct::getMyModuleProductUpdateByIDProduct(
                (int)Tools::getValue('id_product')
            )
        );

        $this->context->smarty->assign($data);

        return $this->display(__FILE__, 'AdminCatalogue.tpl');
    }

    /* Hook display in tab AdminCatalog */
    public function hookDisplayAdminCatalog($params)
    {
        $html = '
            <a href="' . $this->context->link->getAdminLink('MyModuleAdminController', true) . '" class="button">
            <img src="../img/admin/cms.gif" alt="" /> ' . $this->l('historique modif') . '</a><br/<br/>>
        ';

        return $html;
    }

    ############################################################################################################
    # Hook Action
    ############################################################################################################

    public function hookActionProductUpdate($params)
    {
        $this->myModuleLogError("hookActionProductUpdate: hook action call", 0);
        $id_product = (int)$params['id_product'];
        $product = new Product($id_product);

        if (!Validate::isLoadedObject($product)) {
            $this->myModuleLogError("Erreur no validate product", 4);

            return;
        }

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . Tools::strtolower($this->name) . '_productupdate` (
            `id_product`, `date_modification`
        )
        VALUES (
        ' . (int)$id_product . ', NOW()
        )';
        if (DB::getInstance()->execute($sql) === false) {
            $this->myModuleLogError("erreur d'enregistrement de modification produit en bdd");
        }

        $mail_admin = (string)Configuration::get('MA_MERCHANT_MAILS');
        try {
            Mail::Send(
                $default_lang = (int)Configuration::get('PS_LANG_DEFAULT'),
                'updateproduct',
                Mail::l('Product modification', $default_lang),
                array(
                    '{product_name}' => $product->name,
                    '{product_url}'  => $product->getLink()
                ),
                $mail_admin,
                null,
                (string)Configuration::get('PS_SHOP_EMAIL'),
                (string)Configuration::get('PS_SHOP_NAME'),
                null,
                null,
                dirname(__FILE__) . '/mails/',
                false,
                (int)$this->context->shop->id
            );
            $this->myModuleLogError("actionProductUpdate: mail envoyé à " . $mail_admin, 0);
        } catch (Exception $e) {
            $this->myModuleLogError($e, 4);
        }
    }

    ############################################################################################################
    # Configuration functions // Administration
    ############################################################################################################

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $my_module_name = (string)Tools::getValue('MYMODULE_NAME');
            if (!$my_module_name ||
                empty($my_module_name) ||
                !Validate::isGenericName($my_module_name)
            ) {
                $output .= $this->displayError($this->l('Settings updated'));
            } else {
                Configuration::updateValue('MYMODULE_NAME', $my_module_name);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        if (_PS_VERSION_ >= '1.5') {
            return $output . $this->displayForm();
        } else {
            return $output . $this->displayForm14();
        }
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input'  => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Configuration value'),
                    'name'     => 'MYMODULE_NAME',
                    'size'     => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');

        return $helper->generateForm($fields_form);
    }

    // Compatibility 1.4
    public function displayForm14()
    {
        if (_PS_VERSION_ < '1.5') {
            global $currentIndex;

            $defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
            $languages = Language::getLanguages();
            $obj = $this;

            $form = '<script type="text/javascript">
                    id_language = Number(' . $defaultLanguage . ');
              </script>';

            $form .= '
                <form action="' . $currentIndex . '&submitAdd' . $this->table . '=1&token=' .
                Tools::getAdminTokenLite('AdminModules') . '" method="post" class="width3">' .
                ($obj->id ? '<input type="hidden" name="id_' . $this->table . '" value="' . $obj->id . '" />' : '') .
                '<fieldset><legend><img src="../img/admin/profiles.png" />' . $this->l('Profiles') . '</legend><label>' .
                $this->l('Name:') . ' </label><div class="margin-form">';

            foreach ($languages as $language) {
                $form .= '
                <div id="name_' . $language['id_lang' | 'id_lang'] . '" style="display: ' .
                    ($language['id_lang' | 'id_lang'] == $defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input size="33" type="text" name="name_' . $language['id_lang' | 'id_lang'] . '" value="' .
                    htmlentities(Configuration::get('MYMODULE_NAME', (int)$language['id_lang' | 'id_lang'])) .
                    '" /><sup>*</sup>
                </div>';
            }
            $this->displayFlags($languages, $defaultLanguage, 'name', 'name');
            $form .= '
            <div class="clear"></div>
            </div>
            <div class="margin-form">
            <input type="submit" value="' . $this->l('Save') . '" name="submitAdd' . $this->table . '" class="button" />
            </div>
            <div class="small"><sup>*</sup> ' . $this->l('Required field') . '</div>
            </fieldset>
            </form>
            ';

            return $form;
        }

    }

    ############################################################################################################
    # Logger
    ############################################################################################################

    public function myModuleLogError($object, $error_level = 0)
    {
        $error_type = array(
            0 => "[ALL]",
            1 => "[DEBUG]",
            2 => "[INFO]",
            3 => "[WARN]",
            4 => "[ERROR]",
            5 => "[FATAL]"
        );
        $date = date("<Y-m-d(H:m:s)>");
        $stderr = fopen(_PS_MODULE_DIR_ . $this->name . '/error_mymodule.log', 'a');
        fwrite($stderr, $error_type[$error_level] . " " . $date . " " . print_r($object, true) . "\n");
        fclose($stderr);
    }
}

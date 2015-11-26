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

    public function install()
    {
        if (_PS_VERSION_ >= '1.5') {
            if (Shop::isFeatureActive()) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
        }

        return parent::install() &&
        $this->registerHook('leftColumn') &&
        $this->registerHook('header') &&
        Configuration::updateValue('MYMODULE_NAME', 'my friend');
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        ) {
            return false;
        }

        return true;
    }

    // Configuration functions

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
        global $currentIndex;

        $defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages();
        $obj = $this;

        $form = '<script type="text/javascript">
                    id_language = Number(' . $defaultLanguage . ');
              </script>';

        $form .= '
            <form action="' . $currentIndex . '&submitAdd' . $this->table . '=1&token=' . Tools::getAdminTokenLite('AdminModules') . '" method="post" class="width3">
            ' . ($obj->id ? '<input type="hidden" name="id_' . $this->table . '" value="' . $obj->id . '" />' : '') . '
            <fieldset><legend><img src="../img/admin/profiles.png" />' . $this->l('Profiles') . '</legend>
            <label>' . $this->l('Name:') . ' </label>
            <div class="margin-form">';

        foreach ($languages as $language) {
            $form .= '
                <div id="name_' . $language['id_lang' | 'id_lang'] . '" style="display: ' .
                ($language['id_lang' | 'id_lang'] == $defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input size="33" type="text" name="name_' . $language['id_lang' | 'id_lang'] .
                '" value="' . htmlentities(Configuration::get('MYMODULE_NAME', (int)$language['id_lang' | 'id_lang'])) .
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
            </form> ';

        return $form;
    }

    // Hook functions

    public function hookDisplayLeftColumn($params)
    {
        return $this->hookLeftColumn($params);
    }

    public function hookLeftColumn($params)
    {

        if (_PS_VERSION_ < '1.5') {
            $my_module_link = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/sendtoMymodule.php?display=true&token=' . Tools::getAdminTokenLite('AdminModules');
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
}

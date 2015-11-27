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

class MyModuleAdminController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'mymodule_productupdate';
        $this->className = 'MyModuleProduct';
        $this->lang = false;
        $this->bootstrap = true;

        $this->_select = null;
        $this->_join = null;

        $this->fields_list = array(
            'id_product'        => array(
                'title' => $this->l('Product'),
                'width' => 20
            ),
            'date_modification' => array(
                'title' => $this->l('Date'),
                'width' => 20
            )
        );

        parent::__construct();
        $this->identifier = 'id_mymodule';

        $this->addRowAction('view');
        $this->show_toolbar_options = true;
    }

    /**
     * Install Module tab on Admin
     * @param $menu_id
     * @param $module_name
     * @return bool|int
     */
    public function install($menu_id, $module_name)
    {
        return MAC_TotAdminTabHelper::addAdminTab(array(
            'id_parent'    => $menu_id,
            'className'    => 'MyModuleAdmin',
            'default_name' => 'Product Update',
            'name'         => 'Product Update',
            'active'       => true,
            'module'       => $module_name
        ));
    }

    public function renderList()
    {
        return parent::renderList();
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function renderForm()
    {
        return parent::renderForm();
    }
}
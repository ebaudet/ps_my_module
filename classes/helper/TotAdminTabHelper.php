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

class TotAdminTabHelper
{
    /**
     * Function to delete admin tabs from a menu with the module name
     * @param string $name name of the module to delete
     * @return bool
     */
    public static function deleteAdminTabs($name)
    {
        // Get collection from module if tab exists
        $tabs = Tab::getCollectionFromModule($name);
        // Initialize result
        $result = true;
        // Check tabs
        if ($tabs && count($tabs)) {
            // Loop tabs for delete
            foreach ($tabs as $tab) {
                $result &= $tab->delete();
            }
        }

        return $result;
    }

    /**
     * Add admin tabs in the menu
     * @param Array $tabs
     *                Array[
     *                Array[
     *                id_parent => 0 || void
     *                className => Controller to link to
     *                module => modulename to easily delete when uninstalling
     *                name => name to display
     *                position => position
     *                ]
     *                ]
     */
    public static function addAdminTab($data)
    {
        // Get ID Parent
        $id_parent = isset($data['id_parent'])
            ? (int)$data['id_parent']
            : (int)Tab::getIdFromClassName($data['classNameParent']);

        // Tab
        $tab = Tab::getInstanceFromClassName($data['className']);

        $tab->id_parent = (int)$id_parent;
        $tab->class_name = $data['className'];
        $tab->module = $data['module'];
        $tab->position = Tab::getNewLastPosition((int)$id_parent);
        $tab->active = 1;

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $tab->name[(int)$lang['id_lang']] = $data['name'];
        }

        if (!$tab->save()) {
            return false;
        }

        return $tab->id;
    }
}

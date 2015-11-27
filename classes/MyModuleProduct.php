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

class MyModuleProduct extends ObjectModel
{
    public static $definition = array(
        'table'     => 'mymodule_productupdate',
        'primary'   => 'id_mymodule_productupdate',
        'multilang' => false,
        'fiels'     => array(
            'id_product'        => array(
                'type' => parent::TYPE_INT
            ),
            'date_modification' => array(
                'type' => parent::TYPE_DATE
            )
        )
    );

    /**
     * @param null       $id_product
     * @param bool|false $period
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getMyModuleProductUpdates($id_product = null, $period = false)
    {
        $sql = '
            SELECT `' . self::$definition['primary'] . '`
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` AS mp
            ' . Shop::addSqlAssociation(self::$definition['primary'], 'mp') . '
            WHERE 1 ';

        if (is_null($id_product) === false) {
            $sql .= " AND mp.id_product = '" . (int)$id_product . "' ";
        }

        if (is_bool($period) === true) {
            $sql .= " AND (mp.date_modification >= '" . strftime('%Y-%m-%d') .
                "' OR mp.date_modification = '0000-00-00') ";
        }

        $objs_ids = Db::getInstance()->executeS($sql);

        $objs = array();

        if ($objs_ids && count($objs_ids)) {
            foreach ($objs_ids as $obj_id) {
                $objs[] = new MyModuleProduct($obj_id[self::$definition['primary']]);
            }
        }

        return $objs;
    }

    /**
     * Get MyModuleProductUpdate by id_product
     * @param            $id_product
     * @param bool|false $period
     * @return mixed|MyModuleProduct
     */
    public static function getMyModuleProductUpdateByIDProduct($id_product, $period = false)
    {
        $object = self::getMyModuleProductUpdates($id_product, $period);

        return ($object && count($object) ? current($object) : new MyModuleProduct());
    }

    public static function install()
    {
        $sql = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '` (
                    `' . self::$definition['primary'] . '` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `id_product` INT NOT NULL,
                    `date_modification` DATE NOT NULL
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $q) {
            if (!Db::getInstance()->execute($q)) {
                return false;
            }
        }

        return true;
    }

    public static function uninstall()
    {
        // Create Category Table in Database
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`';

        foreach ($sql as $q) {
            if (!Db::getInstance()->Execute($q)) {
                return false;
            }
        }

        return true;
    }
}

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

require_once '../../config/settings.inc.php';
require_once '../../config/defines.inc.php';

if (_PS_VERSION_ < '1.5') {
    require_once 'MyModuleFrontController.php';

    //token security for PS 1.4
    if (Tools::getValue('token') == Tools::getAdminTokenLite('AdminModules')) {
        if (Tools::getValue('display') == 'true') {
            $controller = new MyModuleFrontController();

            global $smarty;
            include_once('../../config/config.inc.php');
            include('../../header.php');

            $smarty->display(dirname(__FILE__) . '/views/templates/front/display.tpl');

            include('../../footer.php');
        } else {
            Tools::redirect("../");
        }
    } else {
        Tools::redirect("../");
    }
} else {
    Tools::redirect("../");
}

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

if (_PS_VERSION_ < '1.5')
    include_once 'controllers/front/MyFrontController14.php';
else
    include_once 'controllers/front/MyFrontController15.php';

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

include_once 'mymodule.php';
include_once 'controllers/front/display.php';

//Load the correct class version for PS 1.4 or PS 1.5


class MyModuleFrontController extends MyFrontDisplayController
{

}
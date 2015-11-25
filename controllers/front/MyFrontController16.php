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

include_once _PS_ROOT_DIR_.'/classes/FrontController.php';

class MyFrontDisplayController extends FrontControllerCore
{
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(
            array(
                'coucou' => $this->module->l('salut les gens')
            )
        );
        $this->setTemplate('display.tpl');
    }
}

<?php

class mymoduledisplayModuleFrontController extends ModuleFrontController
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
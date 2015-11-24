<?php

class mymoduledisplayModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(
            array(
                'coucou' => 'salut les gens'
            )
        );
        $this->setTemplate('display.tpl');
    }
}

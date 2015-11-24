{*
* @author 202 Emilien Baudet <ebaudet@202-ecommerce.com>
* @copyright  Copyright (c) 202 ecommerce 2015
* @license    Commercial license
*}
<li>
    <a href="{$base_dir|escape:'htmlall':'UTF-8'}modules/mymodule/mymodule_page.php"
       title="{l s='Click this link' mod='mymodule'}">
        {l s='Click me!' mod='mymodule'}
    </a>
</li>
<!-- Block mymodule -->
<div id="mymodule_block_home" class="block">
    <h4>{l s='Welcome!' mod='mymodule'}</h4>

    <div class="block_content">
        <p>
            {if !isset($my_module_name) || !$my_module_name}
                {capture name='my_module_tempvar'}{l s='World' mod='mymodule'}{/capture}
                {assign var='my_module_tempvar' value=$smarty.capture.my_module_tempvar}
                {$my_module_name|escape:'htmlall':'UTF-8'}
            {/if}
            {l s='Hello, %1$s!' sprintf=$my_module_name mod='mymodule'}
        </p>

        <p>
            {$my_module_message|escape:'htmlall':'UTF-8'}
        </p>
        <ul>
            <li><a href="{$my_module_link|escape:'htmlall':'UTF-8'}" title="{l s='Click this link' mod='mymodule'}">
                    {l s='Click me!' mod=mymodule}
                </a></li>
        </ul>
    </div>
</div>
<!-- /Block mymodule -->
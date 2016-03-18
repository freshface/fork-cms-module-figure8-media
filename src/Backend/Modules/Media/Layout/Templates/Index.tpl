{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblMedia|ucfirst}
    </h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add'}{option:folder}&amp;folder_id={$folder.id}{/option:folder}" class="button icon iconAdd" title="{$lblAddMedia|ucfirst}">
            <span>{$lblAddMedia|ucfirst}</span>
        </a>
    </div>
</div>

<table>
    <tbody>
        <tr>
            <td id="pagesTree">
                <div class="js-tree">
                    {$tree}
                </div>
            </td>
            <td  id="contentHolder">
                
            </td>
        </tr>
    </tbody>
</table>



{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

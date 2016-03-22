{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblMedia|ucfirst}
    </h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add'}{option:folder}&amp;folder_id={$folder.id}{/option:folder}" class="button icon iconAdd" title="{$lblUpload|ucfirst}">
            <span>{$lblUpload|ucfirst}</span>
        </a>
    </div>
</div>

<table width="100%">
    <tbody>
        <tr>
            <td id="pagesTree">
                <div class="js-tree">
                    {$tree}
                </div>
            </td>
            <td width="20">&nbsp;</td>
            <td  id="contentHolder">
                {option:library}
                    
                    <div class="media-items cf">
                    {iteration:library}

                        <div class="media-item media-item-type-{$library.type}">
                            <div class="media-item-media">
                                <div class="media-item-preview" {option:library.is_image} style="background-image:url({$library.preview_file_url});" {/option:library.is_image}>
                                    <a href="{$library.edit_url}">

                                        <div class="media-item-extension">.{$library.extension}</div>
                                        
                                    </a>
                                </div>
                            </div>

                            <div class="media-item-meta">
                                {$library.filename}

                                <a href="{$library.edit_url}">{$lblEdit}</a>
                            </div>
                        </div>

                    {/iteration:library}
                    </div>
                {/option:library}

                {option:!library}

                {/option:!library}
            </td>
        </tr>
    </tbody>
</table>



{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

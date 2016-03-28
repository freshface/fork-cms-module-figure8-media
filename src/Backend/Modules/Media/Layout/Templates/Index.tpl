{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<div class="pageTitle">
    <h2>
        {$lblMedia|ucfirst}
    </h2>
    <div class="buttonHolderRight">
        
        <a href="{$var|geturl:'AddFiles'}{option:folder}&amp;folder_id={$folder.id}{/option:folder}" class="button icon iconAdd" title="{$lblUpload|ucfirst}">
            <span>{$lblUpload|ucfirst}</span>
        </a>

         <a href="{$var|geturl:'ImportFile'}{option:folder}&amp;folder_id={$folder.id}{/option:folder}" class="button icon iconAdd" title="{$lblImportFile|ucfirst}">
            <span>{$lblImportFile|ucfirst}</span>
        </a>
    </div>
</div>

<table width="100%">
    <tbody>
        <tr>
            <td id="pagesTree" style="width:240px;">
                <div class="js-tree">
                    {$tree}
                </div>
                <div class="tree-instructions">
                <p>
                    <small>
                        {$msgTreeInstructions}
                    </small>
                </p>
                </div>
            </td>
            <td width="20">&nbsp;</td>
            <td  id="contentHolder">

                <div class="media-items-container">

                   

                    <div class="media-items-search">

                        {form:filter}

                            {$hidFolderId}

                            <table width="100%">
                                <tr>
                                    <td width="60">
                                        <div class="media-items-search-view">
                                         {iteration:view}
                                            <label for="{$view.id}">{$view.rbtView} {$view.label}</label>
                                        {/iteration:view}
                                        </div>
                                    </td>
                                    <td>
                                         {$ddmType}
                                    </td>
                                    <td>
                                        {$txtSearch}
                                    </td>
                                    <td align="cf">
                                        <input type="submit" class="button pull-right" value="{$lblSubmit|ucfirst}">
                                    </td>
                                </tr>
                            </table>
                        {/form:filter}

                    </div>
                    

                    {form:action}

                     <div class="media-items-actions cf">
                        {$msgSelectAnAction} {$ddmActions}
                        
                         <input type="submit" class="button pull-right" value="{$lblSubmit|ucfirst}">
                    </div>

                    <div class="media-items-result">


                        {option:library}
                            <div class="media-items cf">
                            {iteration:library}

                                <div class="media-item" data-id="{$library.id}">
                                    <label for="ids{$library.id}">
                                    <div class="media-item-inner cf">
                                        
                                        <input type="checkbox" name="ids[]" value="{$library.id}" id="ids{$library.id}" />
                                        <div class="media-item-selected"><i class="fa fa-check"></i></div>

                                        <div class="media-item-hover">
                                            <div class="media-item-hover-inner">
                                                <div class="media-item-hover-meta">
                                                    <p>
                                                        {option:library.name}<strong>{$library.name}</strong> <br>{/option:library.name}
                                                        {option:library.filenname}Filename: {$library.original_filename} <br>{/option:library.filenname}
                                                        Type: {$library.type} <br>
                                                        Last modified: {$library.edited_on|date:'d-m-Y H:i:s'} 
                                                    </p>
                                                </div>
                                                <a href="{$library.edit_url}" class="button">{$lblEdit}</a>
                                            </div>
                                        </div>
                                        <div class="media-item-media">
                                            <div class="media-item-preview">
                                                
                                                {option:library.is_image}<img src="{$library.preview_file_url}" alt="">{/option:library.is_image}
                                                {option:library.is_file}<div class="media-item-extension">.{$library.extension}</div>{/option:library.is_file}
                                                {option:library.is_audio}<div class="media-item-extension">.{$library.extension}</div>{/option:library.is_audio}
                                                {option:library.is_video}
                                                     {option:!library.is_imported}<div class="media-item-extension">.{$library.extension}</div>{/option:!library.is_imported}
                                                      {option:library.is_imported}<img src="{$library.poster_preview_file_url}" alt="">{/option:library.is_imported}
                                                {/option:library.is_video}
                                                
                                            </div>
                                        </div>

                                        <div class="media-item-footer">

                                            {option:library.is_video}
                                                {option:library.is_imported}
                                                    {option:library.data.is_youtube}<i class="fa fa-youtube"></i>{/option:library.data.is_youtube}
                                                    {option:library.data.is_vimeo}<i class="fa fa-vimeo"></i>{/option:library.data.is_vimeo}
                                                {/option:library.is_imported}
                                                 {option:!library.is_imported}
                                                {/option:!library.is_imported}
                                            {/option:library.is_video}
                                            {$library.name}
                                        </div>
                                    </div>
                                    </label>
                                </div>

                            {/iteration:library}
                            </div>
                        {/option:library}

                        {option:!library}
                            <div class="extra-padding">
                            <p>
                                {$msgNoResults}
                            </p>
                            </div>

                        {/option:!library}
                    </div>
                    {/form:action}

                </div>
            </td>
        </tr>
    </tbody>
</table>



{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

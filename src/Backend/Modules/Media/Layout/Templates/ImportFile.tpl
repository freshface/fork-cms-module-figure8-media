{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblImport|ucfirst} {option:folder}{$lblIn} "{$folder.name}"{/option:folder}
    </h2>
</div>

    {option:folder}
    {form:import}
        <div class="box">
            <div class="heading">
                <h3>{$lblFile|ucfirst}</h3>
            </div>
            <div class="content">
               <p>
                    <label for="source">{$lblSource|ucfirst}</label>
                   {$ddmSource} {$ddmSourceError}
               </p>

                <p>
                    <label for="url">{$lblUrl|ucfirst}</label>
                   {$txtUrl} {$txtUrlError}
               </p>

            </div>
        </div>
        
        <div class="fullwidthOptions">
                <a href="{$var|geturl:'Index'}&amp;folder_id={$folder.id}" class="button linkButton">
                    <span>{$lblCancel|ucfirst}</span>
                </a>
            <div class="buttonHolderRight">

               <a href="#" class="button mainButton submitButton">{$lblImport|ucfirst}  {$lblIn} "{$folder.name}"</a>


            </div>
        </div>
     {/form:import}
    {/option:folder}

    {option:!folder}
        <div id="selectFolderDialog" class="forkForms" title="{$lblSelectFolder|ucfirst}" style="display: none;">
            <p>
                {$msgSelectAFolderToUploadIn} {$ddmFolder}
            </p>
        </div>
    {/option:!folder}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

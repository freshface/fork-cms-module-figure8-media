{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblUpload|ucfirst}
    </h2>
</div>

    {option:folder}
    <form>
        <div class="box">
            <div class="heading">
                <h3>{$lblFiles|ucfirst}</h3>
            </div>
            <div class="content">
                <div id="js-uploadify-queue"></div>
                <p>
                    <input id="js-uploadify" name="files" type="file" multiple="true">
                </p>
            </div>
        </div>
        
        <div class="fullwidthOptions">
                <a href="{$var|geturl:'Index'}&amp;folder_id={$folder.id}" class="button linkButton">
                    <span>{$lblCancel|ucfirst}</span>
                </a>
            <div class="buttonHolderRight">

               <a href="#" class="button mainButton js-upload-start">{$lblUpload|ucfirst}</a>

            </div>
        </div>
    </form>
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

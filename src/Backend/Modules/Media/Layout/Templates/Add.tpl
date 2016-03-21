{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblUpload|ucfirst}
    </h2>
</div>

<form>
    <div class="content">
        
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
    </div>
    <div class="fullwidthOptions">
            <a href="{$var|geturl:'Index'}&amp;folder_id={$folder.id}" class="button linkButton">
                <span>{$lblCancel|ucfirst}</span>
            </a>
        <div class="buttonHolderRight">

           <a href="#" class="button mainButton js-upload-start">{$lblUploadImages|ucfirst}</a>

        </div>
    </div>
 
</form>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

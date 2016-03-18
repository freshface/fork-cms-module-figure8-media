{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblAddMedia|ucfirst}
    </h2>
</div>



<form>
    
    <div class="content">
        
        <div class="box">
            <div class="heading">
                <h3>{$lblImages|ucfirst}</h3>
            </div>
            <div class="content">
                <div id="queue"></div>
                <p>
                    <input id="images" name="images" type="file" multiple="true">
                </p>
            </div>
        </div>
    </div>
    <div class="fullwidthOptions">
            <a href="{$var|geturl:'Edit'}" class="button linkButton">
                <span>{$lblCancel|ucfirst}</span>
            </a>
        <div class="buttonHolderRight">

           <a href="#" class="button mainButton js-upload-start">{$lblUploadImages|ucfirst}</a>

        </div>
    </div>
 
</form>

<script type="text/javascript">
    var uploadTimestamp = '{$timestamp}';
    var uploadToken = '{$token}';
    var uploadScript = '/backend/ajax/?module=Media&action=Upload';
    
</script>


{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

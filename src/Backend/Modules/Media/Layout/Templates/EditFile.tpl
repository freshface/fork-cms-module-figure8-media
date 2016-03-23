{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMedia|ucfirst}: {$lblEdit}</h2>

     <div class="buttonHolderRight">
        <a href="{$var|geturl:'Index'}" class="button icon iconBack" title="{$lblBack|ucfirst}">
            <span>{$lblBack|ucfirst}</span>
        </a>
    </div>

</div>


{form:edit}
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td id="leftColumn">
            	<div class="box">
                    <div class="heading">
                        <h3>
                            <label for="image">{$lblContent|ucfirst}</label>
                        </h3>
                    </div>
                    <div class="options">

		                 <div class="tabs">
		                    <ul>
		                       {iteration:languages}<li><a href="#tab{$languages.abbreviation|uppercase}">{$languages.label|ucfirst}</a></li>{/iteration:languages}
		                    </ul>

		                    {iteration:languages}
		                        <div id="tab{$languages.abbreviation|uppercase}">

		                            <p>
		                                <label for="name{$languages.abbreviation|ucfirst}">{$lblName|ucfirst}</label>
		                                {$languages.txtName} {$languages.txtNameError}
		                            </p>

		                            <div class="box">
		                                <div class="heading">
		                                    <h3>{$lblDescription|ucfirst}</h3>
		                                </div>
		                                <div class="optionsRTE">
		                                    {$languages.txtText} {$languages.txtTextError}
		                                </div>
		                            </div>

		                        </div>
		                    {/iteration:languages}
		                 </div>
		            </div>
				</div>


                {option:record.is_image}
                <div class="box">
                    <div class="heading">
                        <h3>
                            <label for="image">{$lblImage|ucfirst}</label>
                        </h3>
                    </div>
                    <div class="options">
                        <p>
                            <img src="{$record.preview_file_url}">
                        </p>
                    </div>
                </div>
                {/option:record.is_image}

             </td>

             <td id="sidebar">

              <div class="box">
                    <div class="heading">
                        <h3>
                            <label for="folder">{$lblFolder|ucfirst}</label>
                        </h3>
                    </div>
                    <div class="options">
                        {$ddmFolder} {$ddmFolderError}
                    </div>
                </div>
				
				

            </td>
        </tr>
    </table>

    <div class="fullwidthOptions">
        <a href="{$var|geturl:'DeleteFile'}&amp;id={$record.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
            <span>{$lblDelete|ucfirst}</span>
        </a>
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
        </div>
    </div>

    <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
        <p>
            {$msgConfirmDeleteFile}
        </p>
    </div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMedia|ucfirst}: {$lblEditFile}</h2>

     <div class="buttonHolderRight">
        <a href="{$var|geturl:'Index'}&amp;folder_id={$record.folder_id}" class="button icon iconBack" title="{$lblBack|ucfirst}">
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


				<div id="replaceFileDialog" class="forkForms" title="{$lblReplaceFile|ucfirst}" style="display: none;">
	                <div id="js-uploadify-queue"></div>
	                <p>
	                    <input id="js-uploadify" name="files" type="file" multiple="true">
	                </p>
				</div>

                {option:record.is_image}
                <div class="box">
                    <div class="heading">
                        <h3>
                            {$lblPreview|ucfirst}
                        </h3>
                    </div>
                    <div class="options">
                        <p>
                            <img src="{$record.preview_file_url}" id="editableimage">
                        </p>
                    </div>
                     <div class="options">
						<p>
							<a href="#" class="button js-replace-file">{$lblReplace|ucfirst}</a> 
							<a class="button" href="#" onclick="return launchEditor('editableimage', '{$record.file_url}');">{$lblEditImage|ucfirst}!</a>
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


                {option:!record.is_image}
                <div class="box">
                    <div class="heading">
                        <h3>
                            {$lblFile|ucfirst}
                        </h3>
                    </div>
                    <div class="options">
                        <p>
                            <a target="_blank" href="{$record.file_url}">{$record.filename}</a>
                        </p>
                    </div>

                     <div class="options">
						<p><a href="#" class="button js-replace-file">{$lblReplaceFile|ucfirst}</a></p>
                     </div>
                </div>
                {/option:!record.is_image}
				
				

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
            {$msgConfirmDelete}
        </p>
    </div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}

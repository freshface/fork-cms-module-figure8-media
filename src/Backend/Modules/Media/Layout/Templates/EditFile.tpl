{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMedia|ucfirst}: {$lblEditFile}</h2>

     <div class="buttonHolderRight">
        <a href="{$var|geturl:'Index'}&amp;folder_id={$record.folder_id}&amp;{$filter}" class="button icon iconBack" title="{$lblBack|ucfirst}">
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
                     <div class="options button-row">
						<p>
							<a href="#" class="button js-replace-file">{$lblReplace|ucfirst}</a>
                            
                            {option:feather_api_key}

    							{option:allow_feather_edit}
    							<a class="button js-launch-feather-editor" data-image="{$record.file_url}" data-id="editableimage" href="#">{$lblEditImage|ucfirst}</a>
    							<a href="#" data-id="editableimage" class="button js-reset-file {option:!record.is_modified} hide{/option:!record.is_modified}">{$lblReset|ucfirst}</a>
    							{/option:allow_feather_edit}
                            {/option:feather_api_key}
						</p>
						

                     </div>
                </div>
                {/option:record.is_image}
                    
                {option:record.is_video}
                    {option:!record.is_imported}
                    <div class="box">
                        <div class="heading">
                            <h3>
                                {$lblPreview|ucfirst}
                            </h3>
                        </div>
                        <div class="options">
                            

                            <video id="my-video" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="700" height="400" {*poster="MY_VIDEO_POSTER.jpg"*} data-setup="{}">
    						    <source src="{$record.file_url}" type='video/mp4'>
    						    <p class="vjs-no-js">
    						      To view this video please enable JavaScript, and consider upgrading to a web browser that
    						      <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    						    </p>
    						  </video>

    					</div>
                         <div class="options">
    						<p>
    							<a href="#" class="button js-replace-file">{$lblReplace|ucfirst}</a>
    						</p>
                         </div>
                    </div>
                    {/option:!record.is_imported}

                    {option:record.is_imported}
                    <div class="box">
                        <div class="heading">
                            <h3>
                                {$lblPreview|ucfirst}
                            </h3>
                        </div>
                        <div class="options">
                            {option:record.data.is_youtube}
                                <div class="embed-container"><iframe src="http://www.youtube.com/embed/{$record.data.source_id}" frameborder="0" allowfullscreen></iframe></div>
                            {/option:record.data.is_youtube}
                            {option:record.data.is_vimeo}
                               <div class="embed-container"><iframe src="http://player.vimeo.com/video/{$record.data.source_id}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>

                            {/option:record.data.is_vimeo}
                        </div>
                        
                    </div>
                    {/option:record.is_imported}


                    <div class="box">
                        <div class="heading">
                            <h3>
                                {$lblPoster|ucfirst}
                            </h3>
                        </div>
                        <div class="options">
                            <p>
                                <img src="{$record.poster_preview_file_url}" id="editableposterimage">
                            </p>
                        </div>
                         <div class="options button-row">
                            <p>
                                <a href="#" class="button js-replace-poster-file">{$lblReplace|ucfirst}</a>
                                
                                {option:feather_api_key}
                                    {option:allow_feather_edit}
                                    <a class="button js-launch-feather-editor" data-image="{$record.poster_file_url}" data-id="editableposterimage" href="#">{$lblEditImage|ucfirst}</a>
                                    <a href="#" data-id="editableposterimage" class="button js-reset-file {option:!record.is_modified} hide{/option:!record.is_modified}">{$lblReset|ucfirst}</a>
                                    {/option:allow_feather_edit}
                                {/option:feather_api_key}
                            </p>

                         </div>
                    </div>



                {/option:record.is_video}


                {option:record.is_audio}
                <div class="box">
                    <div class="heading">
                        <h3>
                            {$lblPreview|ucfirst}
                        </h3>
                    </div>
                    <div class="options">
                        
						
						<audio src="{$record.file_url}" controls="controls">
							Your browser does not support the audio element.
						</audio>

					</div>
                     <div class="options">
						<p>
							<a href="#" class="button js-replace-file">{$lblReplace|ucfirst}</a>
						</p>
                     </div>
                </div>
                {/option:record.is_audio}

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


                {option:record.is_file}
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
                {/option:record.is_file}
				
				

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

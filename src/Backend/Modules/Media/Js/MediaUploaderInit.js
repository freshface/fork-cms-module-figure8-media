if (typeof jsBackend.media == "undefined") {
   jsBackend.media = {};
}

jsBackend.media.uploader =
{
	// constructor
	init: function()
	{		
		$('#folder').change(function(e){
			window.location = jsBackend.data.get('media.add_files_url') + '&folder_id=' + $(this).val();

		});

		if($('#js-uploadify').length > 0)
		{
			$('#js-uploadify').uploadifive({
					'width'				: 'auto',
					'height'				: 'auto',
					'auto'             	: false,
					'debug'				: jsBackend.current.debug,
					'simUploadLimit' 	: 1,
					'formData'         	: {
											
											'timestamp' : jsBackend.data.get('media.upload_timestamp'),
											'token'     : jsBackend.data.get('media.upload_token'),
											'folder_id'     : jsBackend.data.get('media.folder_id'),
											'fork[module]'     : jsBackend.current.module,
											'fork[action]'	: 'Upload',
											'fork[language]'	: jsBackend.current.language
										 },
					'queueID'			: 'js-uploadify-queue',
					'uploadScript' 		: '/backend/ajax',
					'removeCompleted' 	: false,
					'buttonClass'		: 'uploadifive-select-button',
					'fileType'     		: jsBackend.data.get('media.allowed_file_types'),
					'buttonText'		: utils.string.ucfirst(jsBackend.locale.lbl('SelectFiles')),
					'onQueueComplete' 	: function(file, data)
					{ 
						window.location = jsBackend.data.get('media.upload_uploaded_success_url')
					},
					'onFallback'		: function() {
						//window.location = jsBackend.data.get('media.upload_uploaded_fallback_url')
					 }
				});


			$('.js-upload-start').click(function(e){
				e.preventDefault();
				$('#js-uploadify').uploadifive('upload')
			});

			$('.uploadifive-button').removeClass('uploadifive-button');
		}
	}
}

$(jsBackend.media.uploader.init);

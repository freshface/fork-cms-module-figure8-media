if (typeof jsBackend.media == "undefined") {
   jsBackend.media = {};
}

jsBackend.media.edit_poster_file =
{
	// constructor
	init: function()
	{
		var featherEditor = new Aviary.Feather({
	        apiKey: jsBackend.data.get('media.feather_api_key'),
	        onSave: function(imageID, newURL) {
	            
	            // make the call
				$.ajax(
				{
					data:
					{
						fork: { action: 'SaveModifiedPosterImage' },
						id: jsBackend.data.get('media.id'),
						url: newURL,
					},
					success: function(json, textStatus)
					{
						featherEditor.close();

						if(json.code != 200)
						{
							if(jsBackend.debug) alert(textStatus);

							// show message
							jsBackend.messages.add('error', jsBackend.locale.err('ImageCantBeSaved'));

						}
						else
						{
							var data = json.data;

							var img = document.getElementById(imageID);
	            			img.src = data.preview_file_url;

	            			$('.js-reset-file').removeClass('hide');
							
							// show message
							jsBackend.messages.add('success', jsBackend.locale.msg('ImageIsSaved'));
						}
					}
				});
	        }
	    });

	    $('.js-launch-poster-feather-editor').click(function(e){
	    	featherEditor.launch({image: $(this).data('id'), url: $(this).data('image')});
	    	return false;
	    });

	    $('.js-reset-poster-file').click(function(e){

	    	var imageID = $(this).data('id');
	    	
	    	// make the call
			$.ajax(
			{
				data:
				{
					fork: { action: 'ResetModifiedPosterImage' },
					id: jsBackend.data.get('media.id'),
				},
				success: function(json, textStatus)
				{
					featherEditor.close();

					if(json.code != 200)
					{
						if(jsBackend.debug) alert(textStatus);

						// show message
						jsBackend.messages.add('error', jsBackend.locale.err('ImageCantBeReset'));

					}
					else
					{
						var data = json.data;

						var img = document.getElementById(imageID);
            			img.src = data.preview_file_url;
						
						// show message
						jsBackend.messages.add('success', jsBackend.locale.msg('ImageIsReset'));
					}
				}
			});


	    	return false;
	    })
	}
}

$(jsBackend.media.edit_poster_file.init);

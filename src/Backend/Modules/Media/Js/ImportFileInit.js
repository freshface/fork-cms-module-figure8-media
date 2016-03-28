if (typeof jsBackend.media == "undefined") {
   jsBackend.media = {};
}

jsBackend.media.import_file =
{
	// constructor
	init: function()
	{		
		$('#folder').change(function(e){
			window.location = jsBackend.data.get('media.import_file_url') + '&folder_id=' + $(this).val();

		});

		$selectFolderDialog = $('#selectFolderDialog');

		if($selectFolderDialog.length > 0)
		{
			$selectFolderDialog.dialog(
			{
				autoOpen: true,
				draggable: false,
				resizable: false,
				modal: true,
				buttons:
				[
					
				],
				close: function(e, ui)
				{

				}
			});
		}
	}
}

$(jsBackend.media.import_file.init);

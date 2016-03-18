/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the Media module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
jsBackend.media =
{
    // constructor
    init: function()
    {
    	jsBackend.media.tree.init();
    	jsBackend.media.uploader.init();
    }
}

jsBackend.media.uploader =
{
    // constructor
    init: function()
    {
    	$('#images').uploadifive({
				'auto'             : false,
				'formData'         : {
										'simUploadLimit' : 3,
									   'timestamp' : uploadTimestamp,
									   'token'     : uploadToken,
									   'fork[module]'     : 'Photogallery',
									   'fork[action]'	: 'Upload',
									   'fork[language]'	: jsBackend.current.language
				                     },
				'queueID'          : 'queue',
				'uploadScript'     : '/backend/ajax',
				'removeCompleted' : true,
				'fileType'     : 'image',
				'buttonClass': 'uploadifive-select-button',
				'onQueueComplete' : function(file, data)
				{ 
					window.location = uploadSuccessURL
				},
				'onFallback'   : function() {
					window.location = uploadFallbackURL
       			 }
			});


		$('.js-upload-start').click(function(e){
			e.preventDefault();
			$('#images').uploadifive('upload')
		});

		$('.uploadifive-button').removeClass('uploadifive-button');


    }
}

jsBackend.media.tree =
{
    // constructor
    init: function()
    {
    	if($('.js-tree').length > 0)
    	{


	    	$('.js-tree a[data-id=' + jsBackend.data.get('media.folder_id') + ']').closest('li').attr('data-jstree', '{"opened":true,"selected":true}');

	    	$('.js-tree').jstree({
				"core" : {
			      "check_callback" : true
			    },
	    		"plugins" : [ "wholerow", "dnd", "contextmenu"],
	    		contextmenu : {
	    			items : function (o, cb) { // Could be an object directly
						return {
							"create" : {
								"separator_before"	: false,
								"separator_after"	: true,
								"_disabled"			: false, //(this.check("create_node", data.reference, {}, "last")),
								"label"				: "Create",
								"action"			: function (data) {
									var inst = $.jstree.reference(data.reference),
										obj = inst.get_node(data.reference);
									inst.create_node(obj, {}, "last", function (new_node) {
										//setTimeout(function () { inst.edit(new_node); },0);

										inst.open_node(new_node.parent);	
										
										var parentId = $('#' + new_node.parent).find('a').first().data('id');
								    	var name = 'New node';

								    	// make the call
										$.ajax(
										{
											data:
											{
												fork: { action: 'CreateFolder' },
												parent_id: parentId,
												name: name,
											},
											success: function(json, textStatus)
											{
												if(json.code != 200)
												{
													if(jsBackend.debug) alert(textStatus);

													// show message
													jsBackend.messages.add('error', jsBackend.locale.err('CantBeCreated'));

												}
												else
												{
													var id = json.data;
													new_node.a_attr['data-id'] = id;
													setTimeout(function () { inst.edit(new_node); },0);

													//$('.js-tree').jstree(true).set_id(new_node,id);

													// show message
													jsBackend.messages.add('success', jsBackend.locale.msg('FolderIsCreated'));
												}
											}
										});


									});

									
								}
							},
							"rename" : {
								"separator_before"	: false,
								"separator_after"	: false,
								"_disabled"			: false, //(this.check("rename_node", data.reference, this.get_parent(data.reference), "")),
								"label"				: "Rename",
								/*!
								"shortcut"			: 113,
								"shortcut_label"	: 'F2',
								"icon"				: "glyphicon glyphicon-leaf",
								*/
								"action"			: function (data) {
									var inst = $.jstree.reference(data.reference),
										obj = inst.get_node(data.reference);
									inst.edit(obj);
								}
							}
						}
					}
	    		}
	    	});

	    	$(document).on('dnd_stop.vakata', function (data, element, helper, event) {
	    		var id = $('#' + element.element.id).data('id');
	    		var to = $(element.event.target).data('id');

	    	
			    
			    // make the call
				$.ajax(
				{
					data:
					{
						fork: { action: 'MoveFolder' },
						id: id,
						dropped_on: to,
					},
					success: function(json, textStatus)
					{
						if(json.code != 200)
						{
							if(jsBackend.debug) alert(textStatus);

							// show message
							jsBackend.messages.add('error', jsBackend.locale.err('CantBeMoved'));

						}
						else
						{
							// show message
							jsBackend.messages.add('success', jsBackend.locale.msg('FolderIsMoved'));
						}
					}
				});
			});

			$(".js-tree").bind('select_node.jstree', function(node, selected, event) {
			   
			    var id = $('#' + selected.node.id).find('a').first().data('id');
			    window.location = '?folder_id=' + id;

			});

			 
			 $('.js-tree').on('rename_node.jstree', function (node, text, old) {

			 	console.log($('#' + text.node.id).find('a').first())
		    	var id = $('#' + text.node.id).find('a').first().data('id');
		    	var name = text.text;

		    	
		    	// make the call
				$.ajax(
				{
					data:
					{
						fork: { action: 'RenameFolder' },
						id: id,
						name: name,
					},
					success: function(json, textStatus)
					{
						if(json.code != 200)
						{
							if(jsBackend.debug) alert(textStatus);

							// show message
							jsBackend.messages.add('error', jsBackend.locale.err('CantBeRenamed'));

						}
						else
						{
							// show message
							jsBackend.messages.add('success', jsBackend.locale.msg('FolderIsRenamed'));
						}
					}
				});

			});

	    }
	}
}




$(jsBackend.media.init);

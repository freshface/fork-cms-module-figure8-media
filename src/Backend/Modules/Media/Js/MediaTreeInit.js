if (typeof jsBackend.media == "undefined") {
   jsBackend.media = {};
}

jsBackend.media.tree =
{
	// constructor
	init: function()
	{
		$("input[name=view]:radio").click(function () {
			if($(this).val() == 'list') {
				$('body').addClass('media-items-container-list');
			} else {
				$('body').removeClass('media-items-container-list');
			}
		});

		if(utils.url.getGetValue('view') == 'list') $('body').addClass('media-items-container-list');

		$('.media-items .media-item label').click(function(e){
			
			$(this).closest('.media-item').toggleClass('selected');

			var selected = $(".media-items .media-item input[type='checkbox']:checked");



			if (selected.length > 0) {
			    $('.media-items-actions').slideDown(100);
			} else {
				$('.media-items-actions').slideUp(100);
			}

		})

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
													jsBackend.messages.add('error', jsBackend.locale.err('FolderCantBeCreated'));

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


			$('.js-tree').on('move_node.jstree', function (node, parent, position, old_parent, old_position, is_multi, old_instance, new_instance) {
                var to = $('#' + parent.parent).find('a').first().data('id');

                $('.js-tree').jstree(true).open_node(('#' + parent.parent));

                var id =  $('#' + parent.node.id).find('a').first().data('id');
                var ids = $('#' + parent.parent + ' > ul a').map(function() { return $(this).data('id'); }).get();
                	ids = ids.join();

                // make the call
				$.ajax(
				{
					data:
					{
						fork: { action: 'MoveFolder' },
						id: id,
						dropped_on: to,
						ids: ids
					},
					success: function(json, textStatus)
					{
						if(json.code != 200)
						{
							if(jsBackend.debug) alert(textStatus);

							// show message
							jsBackend.messages.add('error', jsBackend.locale.err('FolderCantBeMoved'));

						}
						else
						{
							// show message
							jsBackend.messages.add('success', jsBackend.locale.msg('FolderIsMoved'));
						}
					}
				});


            });
			/*
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
			});*/

			$(".js-tree").bind('select_node.jstree', function(node, selected, event) {
			   
				var id = $('#' + selected.node.id).find('a').first().data('id');
				window.location = '?folder_id=' + id;

			});

			 
			 $('.js-tree').on('rename_node.jstree', function (node, text, old) {

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
							jsBackend.messages.add('error', jsBackend.locale.err('FolderCantBeRenamed'));

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




$(jsBackend.media.tree.init);

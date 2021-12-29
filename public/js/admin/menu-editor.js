$(document).ready(function () {

    let id = false;
    let type = 'item';
    let $parent_li = false;
    let edit = false;

    /*window.onbeforeunload = function() {
        return "The text you want the alert box to say.";
    }*/

    $('#MenuItemsBox').on('click', '.btnEdit', function() {
        $('html, body').animate({scrollTop: 0}, 500);

        $parent_li = $(this).closest('.list-group-item');

        id = $parent_li.data('menu-id') ? $parent_li.data('menu-id') : $parent_li.data('category-id');
        type = $parent_li.data('menu-id') ? 'item' : 'category';

        $('#menu_id').val(id);
        $('#menu_type').val(type);

        if (type == 'item') {
            let allData = $parent_li.data();

            jQuery.each(allData, function(i, val) {
                if (i.indexOf('title-') !== -1) {
                    let name = i.replace("title-","");
                    $('input[name="title[' + name + ']"]').val($parent_li.data('title-' + name));
                }
                if (i.indexOf('tooltip-') !== -1) {
                    let name = i.replace("tooltip-","");
                    $('input[name="tooltip[' + name + ']"]').val($parent_li.data('tooltip-' + name));
                }
            });

            $('#menu_url').val($parent_li.data('url'));
            $('#menu_target').val($parent_li.data('target'));

            if ($parent_li.data('new') == '1')
                $("#menu_new").prop('checked', true);
            else
                $("#menu_new").prop('checked', false);

            toggleForm(type);
        } else {
            toggleForm(type);
            clearForm(true);

            let allData = $parent_li.data();

            jQuery.each(allData, function(i, val) {
                if (i.indexOf('title-') !== -1) {
                    let name = i.replace("title-","");
                    $('input[name="title[' + name + ']"]').val($parent_li.data('title-' + name));
                }
            });
        }

        $('#UpdateMenu').prop('disabled', false);
    });

    $('#UpdateMenu').on('click', function() {
        $.ajax({
            type: 'POST',
            url: '/admin-cp/menu/update',
            data: $('#frmEdit').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    $parent_li.find('span.txt').first().text($('input[name="title[1]"]').val());

                    $('.lang_title').each(function(i,elem) {
                        $parent_li.data('title-' + $(elem).data('id'), $(elem).val());
                    });

                    $('.lang_tooltip').each(function(i,elem) {
                        $parent_li.data('tooltip-' + $(elem).data('id'), $(elem).val());
                    });

                    $parent_li.data('url', $('#menu_url').val());
                    $parent_li.data('target', $('#menu_target').val());

                    clearForm();
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });
    });

	$(document).on("click", ".sw_custom_button", function(e){
		e.preventDefault();
		var category = $(this).data("val");


        $.ajax({
            type: 'POST',
            url: '/admin-cp/menu/add',
            data: $('#frmEdit').serialize() + '&category=' + category,
            success: function(data) {
                if (data.status == 'success') {
                    $('#cont').html(data.html);
                    
                    if(category=='conv'){
                    	ConvMenuSort[data.data.menu_id] = data.data.sort;
                    }else if(category=='footer'){
                        MainMenuSort[data.data.menu_id] = data.data.sort;                    	
                    }else if (category == 'main') {
                        MainMenuSort[data.data.menu_id] = data.data.sort;
                    } else {
                        ToolsMenuSort[data.category_id]['items'][data.data.menu_id] = data.data.sort;
                    }
                    console.log(data);
                    alert();

                    menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});
                    menuEditor('AllTools', {listOptions: optionsTools, labelEdit: 'Edit', labelRemove: 'X'});

                    ScrollAndShow($('li.list-group-item[data-menu-id="'+data.data.menu_id+'"]'));

                    clearForm();

                } else {

                    swal('Error', data.message, 'error');

                }

            }

        });




	
	});

    $('#AddMenu').on('click', function() {
        if (edit) {
            Swal('Error', 'Save or cancel the sort menu.', 'error');
            return false;
        }

        if (type == 'item') {
			swal({   
				title: 'Custom buttons',
				type: "question",
				html: `Where to place this menu item?
					<hr><button data-val="main" class="sw_custom_button to_main_menu">Main Menu</button>
						<button data-val="tools" class="sw_custom_button to_tools_menu">Tools Menu</button>
						<button data-val="footer" class="sw_custom_button to_convert_menu">Footer Menu</button>						
						<button data-val="conv" class="sw_custom_button to_convert_menu">Convert Menu</button>`, 

				showConfirmButton: false 
			});
			return false;






            Swal({
                title: 'Where to place this menu item?',
                text: "",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Main Menu',
                cancelButtonText: 'Tools Menu',
                showAltActionButton: true,
                
            }).then((result) => {
                let category = false;

                if (result.value) {
                    category = 'main';
                } else if (result.dismiss == 'cancel') {
                    category = 'tools';
                }

                if (category) {
                    $.ajax({
                        type: 'POST',
                        url: '/admin-cp/menu/add',
                        data: $('#frmEdit').serialize() + '&category=' + category,
                        success: function(data) {
                            if (data.status == 'success') {
                                $('#cont').html(data.html);
                                if (category == 'main') {
                                    MainMenuSort[data.data.menu_id] = data.data.sort;
                                } else {
                                    ToolsMenuSort[data.category_id]['items'][data.data.menu_id] = data.data.sort;
                                }

                                menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});
                                menuEditor('AllTools', {listOptions: optionsTools, labelEdit: 'Edit', labelRemove: 'X'});

                                ScrollAndShow($('li.list-group-item[data-menu-id="'+data.data.menu_id+'"]'));

                                clearForm();
                            } else {
                                swal('Error', data.message, 'error');
                            }
                        }
                    });
                }
            })
        } else {
            $.ajax({
                type: 'POST',
                url: '/admin-cp/menu/add',
                data: $('#frmEdit').serialize(),
                success: function(data) {
                    if (data.status == 'success') {

                        $('#cont').html(data.html);

                        ToolsMenuSort[data.data.category_id] = {
                            sort: data.data.sort,
                            items: {}
                        };

                        menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});
                        menuEditor('AllTools', {listOptions: optionsTools, labelEdit: 'Edit', labelRemove: 'X'});

                        ScrollAndShow($('li.list-group-item[data-category-id="'+data.data.category_id+'"]'));

                        clearForm();
                    } else {
                        swal('Error', data.message, 'error');
                    }
                }
            });
        }
    });

    $('#SaveMenu').on('click', function() {
        Swal({
            title: 'Save changes?',
            text: "",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Save'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '/admin-cp/menu/save',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        MainMenuSort: MainMenuSort,
                        ToolsMenuSort: ToolsMenuSort,
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#SaveMenu').prop('disabled', true);
                            $('#CancelChanges').prop('disabled', true);
                            edit = false;
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })
    });

    $('#CancelChanges').on('click', function() {
        Swal({
            title: 'Cancel changes?',
            text: "",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '/admin-cp/menu/cancel',
                    data: {
                        _token: $('input[name="_token"]').val(),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#cont').html(data.html);

                            menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});
                            menuEditor('AllTools', {listOptions: optionsTools, labelEdit: 'Edit', labelRemove: 'X'});

                            $('#SaveMenu').prop('disabled', true);
                            $('#CancelChanges').prop('disabled', true);
                            edit = false;
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })
    });

    $('#MenuItemsBox').on('click', '.RemoveMenu', function() {
        $button = $(this);

        Swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '/admin-cp/menu/remove',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        id: $button.data('id'),
                        type: $button.data('type')
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            $button.closest('.list-group-item').remove();
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

    $('#ResetDefault').on('click', function() {
        Swal({
            title: 'Restore defaults?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '/admin-cp/menu/default',
                    data: {
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            window.location.reload();
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

    $('.toggle_form').on('click', function() {
        clearForm();
        toggleForm();
    });

    function ScrollAndShow($elem) {
        $elem.css('background-color', '#56afe2');
        $($elem).css('transition', '0.3s');

        $('html, body').animate({
            scrollTop: $elem.offset().top - 400
        }, 500);

        setTimeout(function() {
            $($elem).css('background-color', '#ffffff');
            setTimeout(function() {
                $($elem).css('transition', 'none');
            }, 300 );
        }, 1200 );
    }

    function clearForm(clear_var = false) {
        if (!clear_var) {
            id = false;
            $parent_li = false;

            $('#menu_id').val('');
            $('#UpdateMenu').prop('disabled', true);
        }

        $('.lang_title').each(function(i,elem) {
            $(elem).val('');
        });

        $('.lang_tooltip').each(function(i,elem) {
            $(elem).val('');
        });

        $('#menu_url').val('');
        $('#menu_target').val('_self');
        $("#menu_new").prop('checked', false);
    }

    function toggleForm(type_select = false) {
        if (type_select != false)
            type = type_select == 'item' ? 'category' : 'item';

        if (type == 'item') {
            type = 'category';
            $('#menu_type').val(type);
            $('.hide_category').hide();
            $('.toggle_form').text('Item menu');
            $('.toggle_form_selected').html('Category <span class="caret"></span>');
        } else {
            type = 'item';
            $('#menu_type').val(type);
            $('.hide_category').show();
            $('.toggle_form').text('Category');
            $('.toggle_form_selected').html('Item menu <span class="caret"></span>');
        }
    }

    function changeSort(menu, menu_id, category = false) {
        if (menu == 'main') {
            $('#myList li.list-group-item').each(function(i,elem) {
                MainMenuSort[parseInt($(elem).data('menu-id'))] = i + 1;
            });
        }

        if (menu == 'tools') {
            if (category) {
                $('#AllTools li.list-group-item[data-category-id]').each(function(i,elem) {
                    ToolsMenuSort[parseInt($(elem).data('category-id'))]['sort'] = i + 1;
                });
            } else {
                let category = false;
                let n = 1;

                $('#AllTools li.list-group-item').each(function(i,elem) {
                    if ($(elem).data('category-id')) {
                        category = parseInt($(elem).data('category-id'));
                        n = 1;
                    } else {
                        ToolsMenuSort[category]['items'][parseInt($(elem).data('menu-id'))] = n;
                        n++;
                    }
                });
            }
        }

        $('#SaveMenu').prop('disabled', false);
        $('#CancelChanges').prop('disabled', false);
        edit = true;
    }

    /* menuEditor plugin */

    var iconPickerOpt = {cols: 5,  footer: false};
    var optionsList = {
        hintCss: {'border': '1px dashed #13981D'},
        placeholderCss: {'background-color': 'gray'},
        ignoreClass: 'btn',
        opener: {
            active: true,
            as: 'html',
            close: '<i class="fa fa-minus"></i>',
            open: '<i class="fa fa-plus"></i>',
            openerCss: {'margin-right': '10px'},
            openerClass: 'btn btn-success btn-xs'
        },
    	isAllowed: function(currEl, hint, target) {
    		if(hint.parents('li').first().data('none')) {
    			hint.css('display', 'none');
    			return false;
    		} else {
    			hint.css('border', '1px dashed #13981D');
    			return true;
    		}
    	},
        complete: function(currEl) {
            changeSort('main', $(currEl).data('menu-id'));
    	}
    };

    menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});

    var optionsTools = {
        hintCss: {'border': '1px dashed #13981D'},
        placeholderCss: {'background-color': 'gray'},
        ignoreClass: 'btn',
        opener: {
            active: true,
            as: 'html',
            close: '<i class="fa fa-minus"></i>',
            open: '<i class="fa fa-plus"></i>',
            openerCss: {'margin-right': '10px'},
            openerClass: 'btn btn-success btn-xs'
        },
    	isAllowed: function(currEl, hint, target) {
    		if(hint.parents('li').first().length == 0 || hint.parents('li').first().data('none')) {
                if (hint.parents('li').first().length == 0  && currEl.data('category-id')) {
        			hint.css('border', '1px dashed #13981D');
        			return true;
                } else {
    			    hint.css('display', 'none');
        			return false;
                }
    		} else {
                if (currEl.data('category-id') && hint.parents('li').first().length != 0) {
    			    hint.css('display', 'none');
        			return false;
                } else {
        			hint.css('border', '1px dashed #13981D');
        			return true;
                }
    		}
    	},
        complete: function(currEl) {
            if ($(currEl).data('menu-id'))
                changeSort('tools', $(currEl).data('menu-id'));
            else
                changeSort('tools', $(currEl).data('category-id'), true);
    	}
    };

    menuEditor('AllTools', {listOptions: optionsTools, labelEdit: 'Edit', labelRemove: 'X'});

    /* Export Import */

    $('body').on('click', '#Export', function() {
        window.location.href = '/admin-cp/menu/export';
    });

    $('#Import').on('click', function() {
        $('#UploadImport').click();
    });

    let upload_import = false;

    $('#UploadImport').change(function(e) {
        if (upload_import == true) {
            swal('Error', 'Wait for the file to load', 'error');
            return false;
        }

        var files = e.target.files;

        var fData = new FormData;
        fData.append('file', $(this).prop('files')[0]);
        fData.append('_token', $('input[name="_token"]').val());
        fData.append('type', 'menu');

        if (typeof $(this).prop('files')[0] != 'undefined')
            $.ajax({
                url: '/admin-cp/import',
                data: fData,
                processData: false,
                contentType: false,
                type: 'POST',
                beforeSend: function() {
                    upload_file = true;
                },
                success: function (data) {
                    if (data.status == 'success') {
                        swal('Success', '', 'success');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        swal('Error', data.message, 'error');
                    }
                },
                complete: function() {
                    upload_import = false;
                }
            });
    });
});

$(document).ready(function () {

    let id = false;
    let type = 'footer';
    let $parent_li = false;
    let edit = false;

    /*window.onbeforeunload = function() {
        return "The text you want the alert box to say.";
    }*/

    $('#MenuItemsBox').on('click', '.btnEdit', function() {
        $('html, body').animate({scrollTop: 0}, 500);

        $parent_li = $(this).closest('.list-group-item');

        id = $parent_li.data('menu-id');
        type = $parent_li.data('type');

        $('#menu_id').val(id);
        $('#menu_type').val(type);

        let allData = $parent_li.data();

        jQuery.each(allData, function(i, val) {
            if (i.indexOf('title-') !== -1) {
                let name = i.replace("title-","");
                $('input[name="title[' + name + ']"]').val($parent_li.data('title-' + name));
            }
        });

        $('#menu_url').val($parent_li.data('url'));
        $('#menu_target').val($parent_li.data('target'));

        if ($parent_li.data('new') == '1')
            $("#menu_new").prop('checked', true);
        else
            $("#menu_new").prop('checked', false);

        $('#UpdateMenu').prop('disabled', false);
    });

    $('#UpdateMenu').on('click', function() {
        $.ajax({
            type: 'POST',
            url: '/admin-cp/footer-menu/update',
            data: $('#frmEdit').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    $parent_li.find('span.txt').first().text($('input[name="title[1]"]').val());

                    $('.lang_title').each(function(i,elem) {
                        $parent_li.data('title-' + $(elem).data('id'), $(elem).val());
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

    $('#AddMenu').on('click', function() {
        if (edit) {
            Swal('Error', 'Save or cancel the sort menu.', 'error');
            return false;
        }

        Swal({
            title: 'Where to place this menu item?',
            text: "",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Footer Menu',
            cancelButtonText: 'Bottom Menu'
        }).then((result) => {
            let type = false;

            if (result.value) {
                type = 'footer';
            } else if (result.dismiss == 'cancel') {
                type = 'bottom';
            }

            if (type) {
                $.ajax({
                    type: 'POST',
                    url: '/admin-cp/footer-menu/add',
                    data: $('#frmEdit').serialize() + '&type_menu=' + type,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#cont').html(data.html);
                            if (type == 'footer') {
                                FooterMenuSort[data.data.menu_id] = data.data.sort;
                            } else {
                                BottomMenuSort[data.data.menu_id] = data.data.sort;
                            }

                            menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});
                            menuEditor('myListBottom', {listOptions: optionsBottom, labelEdit: 'Edit', labelRemove: 'X'});

                            ScrollAndShow($('li.list-group-item[data-menu-id="'+data.data.menu_id+'"]'));

                            clearForm();
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })
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
                    url: '/admin-cp/footer-menu/save',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        FooterMenuSort: FooterMenuSort,
                        BottomMenuSort: BottomMenuSort,
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
                    url: '/admin-cp/footer-menu/cancel',
                    data: {
                        _token: $('input[name="_token"]').val(),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#cont').html(data.html);

                            menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});
                            menuEditor('myListBottom', {listOptions: optionsBottom, labelEdit: 'Edit', labelRemove: 'X'});

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
                    url: '/admin-cp/footer-menu/remove',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        id: $button.data('id')
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
                    url: '/admin-cp/footer-menu/default',
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

        $('#menu_url').val('');
        $('#menu_target').val('_self');
    }

    function changeSort(menu, menu_id) {
        if (menu == 'footer') {
            $('#myList li.list-group-item').each(function(i,elem) {
                FooterMenuSort[parseInt($(elem).data('menu-id'))] = i + 1;
            });
        }

        if (menu == 'bottom') {
            $('#myListBottom li.list-group-item').each(function(i,elem) {
                BottomMenuSort[parseInt($(elem).data('menu-id'))] = i + 1;
            });
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
            changeSort('footer', $(currEl).data('menu-id'));
        }
    };

    menuEditor('myList', {listOptions: optionsList, labelEdit: 'Edit', labelRemove: 'X'});

    var optionsBottom = {
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
            changeSort('bottom', $(currEl).data('menu-id'));
        }
    };

    menuEditor('myListBottom', {listOptions: optionsBottom, labelEdit: 'Edit', labelRemove: 'X'});

    /* Export Import */

    $('body').on('click', '#Export', function() {
        window.location.href = '/admin-cp/footer-menu/export';
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
        fData.append('type', 'footer-menu');

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

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/save-file-by-url', 'FileController@saveFromLink');
Route::get('/save-file-by-url', 'FileController@saveFromLink');
Route::post('/save-file-from-google-drive', 'FileController@saveFileFromGoogleDrive');
Route::get('/save-file-from-google-drive', 'FileController@saveFileFromGoogleDrive');

Route::get('/get-file-test', function () {
  return view('test');
});


Auth::routes();
Route::get('activate/{id}/{token}', 'HomeController@activation')->name('activation');

Route::get('/google-auth', 'Auth\LoginController@google_auth_redirect')->name('google-auth');
Route::get('/google-callback', 'Auth\LoginController@google_callback')->name('google-callback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/contact_form', 'Admin\ContactController@contact_form');


Route::post('/createStripeCharge', 'StripeController@createCharge');



Route::group(['middleware' => 'check-activity'], function () {

    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/home', 'HomeController@home_redirect');

    Route::get('/blog', 'BlogController@index')->name('blog');
    Route::get('/blog/{id}', 'BlogController@article')->name('article');

    Route::get('/developers', 'HomeController@developers')->name('developers');
    Route::get('/teachers', 'HomeController@teachers')->name('teachers');

    Route::prefix('pdf')->group(function () {
        Route::get('edit', "EditPdf@editpdf");
        Route::post('uploadImage', "EditPdf@uploadImage");
        Route::post('uploadSign', "EditPdf@uploadImage");
        Route::post('uploadPDF', "EditPdf@uploadPDF");
        Route::post('uploadEPUB', "EditPdf@uploadEPUB");
        
        Route::post('uploadDOCX', "EditDocx@uploadDOCX");
        Route::post('deleteImage', "EditPdf@deleteImage");

        Route::post('createPdf', "EditPdf@createPdf");
        Route::post('sendByEmail', "EditPdf@sendByEmail");
        Route::post('createShareLink', "EditPdf@createShareLink");       
        Route::get('share', "EditPdf@uploadShare");       
        Route::get('getExternalFile', "EditPdf@getExternalFile");
        Route::get('download_edited/{id}', "EditPdf@downloadEdited");

        Route::get('delete_old_files', "EditPdf@deleteOldFiles");
    });


    Route::get('delete_inactive_users', "HomeController@deleteInActiveUsers");
    

	Route::get('downloadfile/{type}/{uuid}', "EditPdf@downloadFile");
    Route::post("pdf-compress", "ToolController2@compress");
    Route::post("pdf-rotate", "ToolController2@rotate");
    Route::post("pdf-burst", "ToolController2@burst");    
	Route::post("pdf-crop", "ToolController2@crop");
	Route::post("pdf-resize", "ToolController2@resize");	
	Route::post("pdf-mix", "ToolController2@mix");
	Route::post("pdf-to-docx", "ToolController2@pdf2word");
	Route::post("ppt-to-pdf", "ToolController2@ppt2pdf");
	Route::post("pdf-to-ppt", "ToolController2@pdf2ppt");
	Route::get("translate-texts", "ToolController2@translate");
	Route::post("translate-texts", "ToolController2@translate");
	Route::post("epub-to-pdf", "ToolController2@epub2pdf");	
	Route::post("pdf-to-epub", "ToolController2@pdf2epub");

	
	
	Route::post("trans-docx", "ToolController2@transDocx");
	Route::get("trans-docx", "ToolController2@transDocxDebug");
	

	Route::post("translate-block", "ToolController2@transBlock")->name("translate-block");
//	Route::get("translate-block", "ToolController2@transBlock")->name("translate-block-get");

	


    Route::get("testpdf", "ToolController2@testpdf");
	

	
	Route::post("pdf-extract-pages", "ToolController2@extractPages");
	Route::post("pdf-split-by-bookmarks", "ToolController2@extractByOutline");
	Route::post("pdf-encrypt", "ToolController2@encrypt");
	Route::post("pdf-split-by-size", "ToolController2@splitBySize");
	Route::post("pdf-split-by-text", "ToolController2@splitByText");	
	
	Route::post("pdf-header-footer", "ToolController2@headerfooterpdf");
 	Route::post("pdf-bates-numbering", "ToolController2@batesNumbering");   
 	Route::post("pdf-ocr", "ToolController2@ocr");   
 	
 	Route::post("pdf-fill-and-sign-link", "ToolController2@fillAndSignLink");   
 	Route::post("pdf-fill-and-sign-email", "ToolController2@fillAndSignEmail");   

 	Route::post("pdf-to-excel", "ToolController2@excel");   
 	

    Route::group(['middleware' => 'check-auth'], function () {
        Route::get('/account', 'AccountController@index')->name('account');
        
        Route::get('/resend-confirmation', 'AccountController@resendConfirmationEmail');
        Route::get('/change-password', 'AccountController@change_password')->name('change_password');
        Route::post('/account/change_password', 'AccountController@change_password_save');
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::get('/', 'Admin\AdminController@redirect');
        Route::get('/login', 'Admin\AdminController@login')->name('admin-login');

        Route::group(['middleware' => 'check-admin'], function () {
            Route::get('/dashboard', 'Admin\AdminController@dashboard')->name('admin-dashboard');

            Route::get('/profile', 'Admin\AdminController@profile')->name('admin-profile');
            Route::post('/profile', 'Admin\AdminController@save_profile');

            Route::group(['prefix' => 'menu'], function () {
                Route::get('/', 'Admin\MenuController@index')->name('admin-menu');

                Route::post('/update', 'Admin\MenuController@update');
                Route::post('/add', 'Admin\MenuController@add');
                Route::post('/save', 'Admin\MenuController@save');
                Route::post('/remove', 'Admin\MenuController@remove');
                Route::post('/default', 'Admin\MenuController@default_menu');
                Route::post('/cancel', 'Admin\MenuController@cancel');
                Route::get('/export', 'Admin\MenuController@export');
            });

            Route::group(['prefix' => 'footer-menu'], function () {
                Route::get('/', 'Admin\MenuController@footer_index')->name('admin-footer-menu');

                Route::post('/update', 'Admin\MenuController@footer_update');
                Route::post('/add', 'Admin\MenuController@footer_add');
                Route::post('/save', 'Admin\MenuController@footer_save');
                Route::post('/remove', 'Admin\MenuController@footer_remove');
                Route::post('/default', 'Admin\MenuController@footer_default_menu');
                Route::post('/cancel', 'Admin\MenuController@footer_cancel');
                Route::get('/export', 'Admin\MenuController@footer_export');
            });

            Route::get('/users', 'Admin\UserController@index')->name('admin-users');
            Route::group(['prefix' => 'user'], function () {
                Route::get('/edit/{id}', 'Admin\UserController@edit')->name('admin-edit-user');
                Route::get('/show/{id}', 'Admin\UserController@show')->name('admin-show-user');
                Route::get('/login/{id}', 'Admin\UserController@login')->name('admin-login-user');
                Route::post('/delete', 'Admin\UserController@delete');
                Route::post('/update', 'Admin\UserController@update');

                Route::get('/add', 'Admin\UserController@add')->name('admin-add-user');
                Route::post('/add', 'Admin\UserController@add_user');

                Route::post('/delete_document', 'Admin\UserController@delete_document');
            });

            Route::get('/documents', 'Admin\DocumentController@index')->name('admin-documents');

            Route::get('/pages', 'Admin\PageController@index')->name('admin-pages');
            Route::group(['prefix' => 'page'], function () {
                Route::get('/edit/{id}', 'Admin\PageController@edit')->name('admin-edit-page');
                Route::post('/delete', 'Admin\PageController@delete');
                Route::post('/update', 'Admin\PageController@update');

                Route::get('/add', 'Admin\PageController@add')->name('admin-add-page');
                Route::post('/add', 'Admin\PageController@add_page');
                Route::get('/export', 'Admin\PageController@export');
            });

            Route::get('/articles', 'Admin\BlogController@index')->name('admin-articles');
            Route::group(['prefix' => 'article'], function () {
                Route::get('/edit/{id}', 'Admin\BlogController@edit')->name('admin-edit-article');
                Route::post('/delete', 'Admin\BlogController@delete');
                Route::post('/update', 'Admin\BlogController@update');

                Route::get('/add', 'Admin\BlogController@add')->name('admin-add-article');
                Route::post('/add', 'Admin\BlogController@add_article');
            });

            Route::get('/guides', 'Admin\GuideController@index')->name('admin-guides');
            Route::group(['prefix' => 'guide'], function () {
                Route::get('/edit/{id}', 'Admin\GuideController@edit')->name('admin-edit-guide');
                Route::post('/delete', 'Admin\GuideController@delete');
                Route::post('/update', 'Admin\GuideController@update');

                Route::get('/add', 'Admin\GuideController@add')->name('admin-add-guide');
                Route::post('/add', 'Admin\GuideController@add_guide');
                Route::get('/export', 'Admin\GuideController@export');
            });

            Route::get('/languages', 'Admin\LanguageController@index')->name('admin-languages');
            Route::group(['prefix' => 'language'], function () {
                Route::get('/edit/{id}', 'Admin\LanguageController@edit')->name('admin-edit-language');
                Route::post('/delete', 'Admin\LanguageController@delete');
                Route::post('/update', 'Admin\LanguageController@update');

                Route::get('/add', 'Admin\LanguageController@add')->name('admin-add-language');
                Route::post('/add', 'Admin\LanguageController@add_language');
            });

            Route::group(['prefix' => 'faq'], function () {
                Route::get('/', 'Admin\FaqController@index')->name('admin-faq');
                Route::get('/edit/{id}', 'Admin\FaqController@edit')->name('admin-edit-faq');
                Route::post('/delete', 'Admin\FaqController@delete');
                Route::post('/update', 'Admin\FaqController@update');

                Route::get('/add', 'Admin\FaqController@add')->name('admin-add-faq');
                Route::post('/add', 'Admin\FaqController@add_faq');
                Route::get('/export', 'Admin\FaqController@export');
            });

            Route::group(['prefix' => 'setting'], function () {
                Route::get('/seo-global', 'Admin\SettingController@seo')->name('admin-setting-seo');
                Route::post('/seo-global', 'Admin\SettingController@seo_save');

                Route::get('/contacts', 'Admin\SettingController@contacts')->name('admin-setting-contact');
                Route::post('/contacts', 'Admin\SettingController@contacts_save');

                Route::get('/socials', 'Admin\SettingController@socials')->name('admin-setting-social');
                Route::post('/socials', 'Admin\SettingController@socials_save');

                Route::get('/stripe', 'Admin\SettingController@stripe')->name('stripe');
                Route::post('/stripe', 'Admin\SettingController@stripe');

                
                
                Route::get('/translate-pricing', 'Admin\SettingController@translatePricing')->name('admin-setting-translate-pricing');
                Route::post('/translate-pricing', 'Admin\SettingController@translatePricingSave')->name('admin-setting-translate-pricing-save');
                
                
                

                Route::get('/sendgrid', 'Admin\SettingController@sendgrid')->name('admin-setting-sendgrid');
                Route::post('/sendgrid', 'Admin\SettingController@sendgrid_save');
            });

            Route::group(['prefix' => 'option'], function () {
                Route::post('/update', 'Admin\OptionController@update');
            });

            Route::post('/upload_photo', 'Admin\UploadController@upload_ck');
            Route::post('/upload', 'Admin\UploadController@upload');
            Route::post('/import', 'Admin\UploadController@import');
            Route::get('/test', 'Admin\UploadController@test');

            Route::get('/administrators', 'Admin\AdminController@administrators')->name('admin-administrators');

            Route::group(['prefix' => 'administrator'], function () {
                Route::get('/edit/{id}', 'Admin\AdminController@edit')->name('admin-edit-administrator');
                Route::post('/delete', 'Admin\AdminController@delete');
                Route::post('/update', 'Admin\AdminController@update');

                Route::get('/add', 'Admin\AdminController@add')->name('admin-add-administrator');
                Route::post('/add', 'Admin\AdminController@add_administrator');
            });

            Route::get('/contacts', 'Admin\ContactController@index')->name('admin-contacts');

            Route::group(['prefix' => 'contact'], function () {
                Route::get('/show/{id}', 'Admin\ContactController@show')->name('admin-show-contact');
                Route::post('/delete', 'Admin\ContactController@delete');
            });
        });
    });

	Route::group(['middleware'=>'pdf-editor'], function(){
		Route::get('/pdf-editor-fill-sign/{file_id}', 'EditPdf@editpdf');
		Route::get('/pdf-editor-fill-sign', 'EditPdf@editpdf');
		Route::get('/pdf-to-excel', 'EditPdf@editpdf');
		Route::get('/pdf-editor/{file_id}', 'EditPdf@editpdf');		
		Route::get('/pdf-editor', 'EditPdf@editpdf');


	});


    Route::get('/index', function(){
    	return redirect('/');

    });

    Route::get('/{link}', 'HomeController@get_page')->name('get-page');


    Route::prefix('tool')->group(function () {
        Route::post('upload', "ToolController@upload");
        Route::post('pdf-to-jpg', "ToolController@pdf_to_jpg");
        Route::post('pdf-to-txt', "ToolController@pdf_to_txt");
        Route::post('jpg-to-pdf', "ToolController@jpg_to_pdf");
        Route::post('delete-pages', "ToolController@delete_pages");
        Route::post('grayscale-pdf', "ToolController@grayscale_pdf");
        Route::post('merge-pdf', "ToolController@merge_pdf");
        Route::post('html-to-pdf', "ToolController@html_to_pdf");
        Route::post('doc-to-pdf', "ToolController@doc_to_pdf");
        Route::post('n-up-pdf', "ToolController@n_up_pdf");
        Route::post('unlock-pdf', "ToolController@unlock_pdf");
        Route::post('check-lock-pdf', "ToolController@check_lock_pdf");
        Route::post('repair-pdf', "ToolController@repair_pdf");
        Route::post('split-in-half', "ToolController@split_in_half");
        Route::post('get-doc-size', "ToolController@get_doc_size");
        Route::post('pdf-to-excel', "ToolController@pdf_to_excel");
        Route::post('combine-pdf', "ToolController@combine_pdf");
    });

});



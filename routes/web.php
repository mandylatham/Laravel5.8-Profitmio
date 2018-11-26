<?php


//region OUTSIDE API CALLS
Route::any('/text-responses/inbound', 'ResponseConsoleController@inboundText')->middleware(null);
Route::any('/email-responses/inbound', 'ResponseConsoleController@inboundEmail')->middleware(null);
Route::any('/email-responses/log', 'ResponseConsoleController@logEmail')->middleware(null);
Route::any('/phone-responses/inbound', 'ResponseConsoleController@inboundPhone')->middleware(null);
Route::any('/phone-responses/status', 'ResponseConsoleController@inboundPhoneStatus')->middleware(null);

Route::any('/appointments/insert', 'AppointmentController@insert')->middleware(null);
Route::any('/appointments/save', 'AppointmentController@save')->middleware(null);
Route::any('/appointments/get', 'AppointmentController@get')->middleware(null);
//endregion

//region AUTH
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('auth.authenticate');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
//endregion

//region AUTHENTICATED REQUESTS ONLY
Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', 'HomeController@index')->middleware('check.active.company')->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::get('/impersonate/leave', 'AdminController@impersonateLeave')->name('admin.impersonate-leave');
        Route::get('/impersonate/{user}', 'AdminController@impersonateUser')->middleware('can:impersonate,App\Models\User')->name('admin.impersonate');
    });

    Route::group(['prefix' => 'selector'], function () {
        Route::get('', 'SelectorController@show')->name('selector.select-active-company');
        Route::post('', 'SelectorController@updateActiveCompany')->name('selector.update-active-company');
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('', 'UserController@index')->name('user.index')->middleware(['check.active.company', 'can:list,App\Models\User']);
        Route::get('/create', 'UserController@create')->name('user.create')->middleware(['check.active.company', 'can:create-user,App\Models\User']);
        Route::post('', 'UserController@store')->name('user.store')->middleware(['check.active.company', 'can:create-user,App\Models\User']);
    });

    Route::post('/appointment/{appointment}/update-called-status', 'AppointmentController@updateCalledStatus')->middleware('can:change-console');
    Route::post('/callback/{appointment}/update-called-status', 'AppointmentController@updateCalledStatus')->middleware('can:change-console');

    /* TEMPLATES */
    Route::get('/templates', 'TemplateController@index')->middleware('can:view-templates');
    Route::get('/templates/new', 'TemplateController@newForm')->middleware('can:change-templates');
    Route::post('/template/create', 'TemplateController@create')->middleware('can:change-templates');
    Route::group(['prefix' => '/template/{template}', 'middleware' => 'can:view-templates'], function () {
        Route::get('/', 'TemplateController@show');
        Route::post('/json', 'TemplateController@showJson');
        Route::get('/edit', 'TemplateController@editForm')->middleware('can:change-templates');
        Route::post('/update', 'TemplateController@update')->middleware('can:change-templates');
        Route::get('/delete', 'TemplateController@delete')->middleware('can:change-templates');
    });

    Route::get('/template-builder', 'TemplateBuildController@index')->middleware('can:admin-only');
    Route::get('/template-builder/editor', 'TemplateBuildController@showEditor')->middleware('can:admin-only');
    Route::get('/template-builder/templates/{template}/{templateName}', 'TemplateBuildController@getTemplate')->middleware('can:admin-only');
    Route::get('/template-builder/upload', 'TemplateBuildController@getImageList')->middleware('can:admin-only');
    Route::post('/template-builder/upload', 'TemplateBuildController@uploadImage')->middleware('can:admin-only');
    Route::get('/template-builder/img', 'TemplateBuildController@getImage')->middleware('can:admin-only');
    Route::get('/template-builder/templates/{template}/edres/{file}', 'TemplateBuildController@getEdresFile')->middleware('can:admin-only');
    Route::post('/template-builder/dl', 'TemplateBuildController@download')->middleware('can:admin-only');
    Route::get('/template-builder/dl', 'TemplateBuildController@download')->middleware('can:admin-only');
    Route::post('/template-builder/create', 'TemplateBuildController@createTemplate')->middleware('can:admin-only');

    /* CAMPAIGNS */
    Route::get('/campaigns', 'CampaignController@index')->middleware('can:view-campaigns')->name('campaign.index');
    Route::get('/campaigns/user/{user}', 'CampaignController@getUserCampaigns')->middleware('can:view-campaigns');
    Route::get('/campaigns/new', 'CampaignController@createNew')->middleware('can:change-campaigns');
    Route::post('/campaigns/create', 'CampaignController@create')->middleware('can:change-campaigns');
    Route::group(['prefix' => '/campaign/{campaign}', 'middleware' => ['check.active.company','can:view,campaign']], function () {
        Route::get('/', 'CampaignController@show')->name('campaign.view');
        Route::delete('/', 'CampaignController@delete');
        Route::get('/details', 'CampaignController@details');
        Route::get('/edit', 'CampaignController@edit')->name('campaign.edit');
        Route::get('/delete', 'CampaignController@delete')->name('campaign.delete');
        Route::post('/update', 'CampaignController@update');
        // Recipient list pages
        Route::get('/recipients', 'RecipientController@show')->name('campaign.recipient.index');
        Route::post('/recipient-list/upload', 'RecipientController@uploadFile')->name('recipient-list.upload');
        Route::get('/recipient-list/{id}', 'RecipientController@showRecipientList')->name('recipient-list.show');
        Route::get('/recipient-list/delete/{list}', 'RecipientController@deleteRecipientList')->name('recipient-list.delete');
        Route::post('/recipient-list/{list}/delete-stats', 'RecipientController@recipientListDeleteStats')->name('recipient-list.delete-stats');
        Route::post('/recipient-list', 'RecipientController@fromCampaign');
        Route::post('/add-recipient', 'RecipientController@add');
        Route::put('/update-recipient', 'RecipientController@update');
        Route::delete('/remove-recipient', 'RecipientController@delete')->name('recipient.delete');
        Route::get('/recipients/partialByField', 'RecipientController@getPartialRecipientsByField');
        Route::post('/recipients/deletePartialByField', 'RecipientController@deletePartialRecipientsByField');
        Route::get('/recipients/search', 'RecipientController@searchForDeployment');
        Route::post('/recipients/upload', 'RecipientController@createRecipientList');
        Route::any('/recipients/finalize_upload', 'RecipientController@finishUpload');
        Route::get('/recipients/download', 'RecipientController@download');
        Route::get('/recipients/delete-all', 'RecipientController@deleteAll');
        // End of Recipient list pages
        Route::get('phone-list', 'PhoneController@fromCampaign');
        Route::get('/drops', 'DeploymentController@forCampaign')->name('campaign.drop.index');
        Route::get('/drop/{drop}', 'DeploymentController@show');
        Route::post('/drop/{deployment}/update', 'DeploymentController@update');
        Route::get('/drops/new', 'DeploymentController@createNew');
        Route::post('/drops/create', 'DeploymentController@create');
        Route::post('/drops/add-groups', 'DeploymentController@saveGroups');
        Route::post('/drop/{drop}/send-sms/{recipient}', 'DeploymentController@deploySms');
        Route::get('/drop/{drop}/edit', 'DeploymentController@updateForm');
        Route::get('/responses', 'ResponseController@getCampaignResponses');
        Route::get('/responses/export-responders', 'ResponseController@getAllResponders');
        Route::get('/responses/export-nonresponders', 'ResponseController@getNonResponders');
        Route::any('/get-responses-hash', 'ResponseController@getResponsesHash');
        Route::any('/responses/{recipient}/get-text-hash', 'ResponseController@getTextHash');
        Route::any('/responses/{recipient}/get-email-hash', 'ResponseController@getEmailHash');
        Route::any('/responses/{recipient}/get-text-thread', 'ResponseController@getTextThread');
        Route::any('/responses/{recipient}/get-email-thread', 'ResponseController@getEmailThread');
        Route::any('/get-response-list', 'ResponseController@getResponseList');
        Route::get('/response/{recipient}', 'ResponseController@getResponse');
        Route::post('/text-response/{recipient}', 'ResponseConsoleController@smsReply');
        Route::post('/email-response/{recipient}', 'ResponseConsoleController@emailReply')->middleware('can:respond-console');
        Route::get('/response-console', 'ResponseConsoleController@show')->name('campaign.response-console.index');;
        Route::get('/response-console/unread', 'ResponseConsoleController@showUnread');
        Route::get('/response-console/idle', 'ResponseConsoleController@showIdle');
        Route::get('/response-console/archived', 'ResponseConsoleController@showArchived');
        Route::get('/response-console/labelled/{label}', 'ResponseConsoleController@showLabelled');
        Route::get('/response-console/calls', 'ResponseConsoleController@showCalls');
        Route::get('/response-console/sms', 'ResponseConsoleController@showTexts');
        Route::get('/response-console/email', 'ResponseConsoleController@showEmails');
    });

    Route::group(['prefix' => '/recipient/{recipient}', 'middleware' => ['check.active.company','can:update,recipient']], function () {
        Route::post('/add-label', 'RecipientController@addLabel');
        Route::post('/remove-label', 'RecipientController@removeLabel');
        Route::post('/update-notes', 'RecipientController@updateNotes');
    });

    /* DEPLOYMENTS */
    /* TODO: change nomenclature to Drops */
    Route::group(['prefix' => '/drop/{deployment}', 'middleware' => 'can:change-campaigns'], function () {
        Route::get('/pause', 'DeploymentController@pause');
        Route::get('/resume', 'DeploymentController@resume');
        Route::post('/delete', 'DeploymentController@delete');
    });

    /* PHONES */
    Route::group(['prefix' => '/phones', 'middleware' => 'can:change-campaigns'], function () {
        Route::post('search', 'PhoneController@searchAvailable');
        Route::post('provision', 'PhoneController@provision');
        // Route::get('list-unused', 'PhoneController@showUnused'); // Future improvement
        // Route::post('release', 'PhoneController@releaseNumber'); // Future improvement
    });

    /* RESPONSES */
    Route::post('/response/{response}/update-read-status', 'ResponseController@updateReadStatus')->middleware('can:change-console');

    /* SYSTEM */
    Route::get('/system/drops', 'SystemController@index')->middleware('can:admin-only');
    Route::get('/system/reports', 'SystemController@index')->middleware('can:admin-only');

    //region COMPANIES
    Route::group(['prefix' => 'companies'], function () {
        Route::get('', 'CompanyController@index')->middleware('can:create')->name('company.index');
        Route::get('/create', 'CompanyController@create')->middleware('can:create,App\Models\Company')->name('company.create');
        Route::get('/{company}/edit', 'CompanyController@edit')->middleware('can:edit,company')->name('company.edit');
        Route::post('/{company}', 'CompanyController@update')->middleware('can:edit,company')->name('company.update');
        Route::post('/', 'CompanyController@store')->middleware('can:create,App\Models\Company')->name('company.store');
        Route::get('/{company}/campaign', 'CompanyController@campaignIndex')->middleware('can:viewForPreferences,company')->name('company.campaign.index');
        Route::get('/{company}/edit', 'CompanyController@edit')->middleware('can:manage,company')->name('company.edit');
        Route::delete('/{company}', 'CompanyController@delete')->middleware('can:manage,company')->name('company.delete');

        Route::group(['prefix' => '/{company}/user'], function () {
            Route::get('', 'CompanyController@userIndex')->middleware('can:manage,company')->name('company.user.index');
            Route::get('/create', 'CompanyController@userCreate')->middleware('can:manage,company')->name('company.user.create');
            Route::post('', 'CompanyController@userStore')->middleware('can:manage,company')->name('company.user.store');
            Route::get('/{user}/edit', 'CompanyController@userEdit')->middleware('can:manage,company')->name('company.user.edit');
        });
    });
    //endregion
});
//endregion

//region NEW ROUTES
Route::group(['prefix' => 'registration'], function () {
    Route::get('/complete', 'Auth\CompleteController@show')->middleware('signed', 'justinvited')->name('registration.complete.show');
    Route::post('/complete', 'Auth\CompleteController@set')->middleware('signed')->name('registration.complete.store');
});
//endregion

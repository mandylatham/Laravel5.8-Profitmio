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

    Route::group(['prefix' => 'selector'], function () {
        Route::get('', 'SelectorController@show')->name('selector.select-active-company');
        Route::post('', 'SelectorController@updateActiveCompany')->name('selector.update-active-company');
    });

    /* USERS */
    Route::get('/impersonateas/{user}', 'Auth\ImpersonateController@login')->middleware('can:create,App\Models\User')->name('auth.impersonate');
    Route::get('/users', 'UserController@index')->middleware('can:view-users')->name('users.index');
    Route::get('/users/new', 'UserController@createForm')->middleware('can:change-users');
    Route::post('/user/create', 'UserController@create')->middleware('can:change-users');
    Route::group(['prefix' => '/user/{user}'], function () {
        Route::get('/', 'UserController@show')->middleware('can:view-users');
        Route::get('/edit', 'UserController@updateForm')->middleware('can:change-users');
        Route::post('/update', 'UserController@update')->middleware('can:change-users');
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
    Route::get('/campaigns', 'CampaignController@index')->middleware('can:view-campaigns');
    Route::get('/campaigns/user/{user}', 'CampaignController@getUserCampaigns')->middleware('can:view-campaigns');
    Route::get('/campaigns/new', 'CampaignController@createNew')->middleware('can:change-campaigns');
    Route::post('/campaigns/create', 'CampaignController@create')->middleware('can:change-campaigns');
    Route::group(['prefix' => '/campaign/{campaign}'], function () {
        Route::get('/', 'CampaignController@show')->middleware('can:view-campaigns');
        Route::delete('/', 'CampaignController@delete');
        Route::get('/details', 'CampaignController@details')->middleware('can:view-campaigns');
        Route::get('/edit', 'CampaignController@edit')->middleware('can:change-campaigns');
        Route::get('/delete', 'CampaignController@delete')->middleware('can:change-campaigns');
        Route::post('/update', 'CampaignController@update')->middleware('can:change-campaigns');
        // Recipient list pages
        Route::get('/recipients', 'RecipientController@show')->name('recipients')->middleware('can:view-campaigns');
        Route::post('/recipient-list/upload', 'RecipientController@uploadFile')->name('recipient-list.upload')->middleware('can:change-campaigns');
        Route::get('/recipient-list/{id}', 'RecipientController@showRecipientList')->name('recipient-list.show')->middleware('can:view-campaigns');
        Route::get('/recipient-list/delete/{list}', 'RecipientController@deleteRecipientList')->name('recipient-list.delete')->middleware('can:change-campaigns');
        Route::post('/recipient-list/{list}/delete-stats', 'RecipientController@recipientListDeleteStats')->name('recipient-list.delete-stats')->middleware('can:change-campaigns');
        Route::post('/recipient-list', 'RecipientController@fromCampaign')->middleware('can:change-campaigns');
        Route::post('/add-recipient', 'RecipientController@add')->middleware('can:change-campaigns');
        Route::put('/update-recipient', 'RecipientController@update')->middleware('can:change-campaigns');
        Route::delete('/remove-recipient', 'RecipientController@delete')->name('recipient.delete')->middleware('can:change-campaigns');
        Route::get('/recipients/partialByField', 'RecipientController@getPartialRecipientsByField');
        Route::post('/recipients/deletePartialByField', 'RecipientController@deletePartialRecipientsByField');
        Route::get('/recipients/search', 'RecipientController@searchForDeployment')->middleware('can:view-campaigns');
        Route::post('/recipients/upload', 'RecipientController@createRecipientList')->middleware('can:change-campaigns');
        Route::any('/recipients/finalize_upload', 'RecipientController@finishUpload')->middleware('can:change-campaigns');
        Route::get('/recipients/download', 'RecipientController@download')->middleware('can:view-campaigns');
        Route::get('/recipients/delete-all', 'RecipientController@deleteAll')->middleware('can:change-campaigns');
        // End of Recipient list pages
        Route::get('phone-list', 'PhoneController@fromCampaign')->middleware('can:view-campaigns');
        Route::get('/drops', 'DeploymentController@forCampaign')->middleware('can:view-campaigns');
        Route::get('/drop/{drop}', 'DeploymentController@show')->middleware('can:view-campaigns');
        Route::post('/drop/{deployment}/update', 'DeploymentController@update')->middleware('can:change-campaigns');
        Route::get('/drops/new', 'DeploymentController@createNew')->middleware('can:change-campaigns');
        Route::post('/drops/create', 'DeploymentController@create')->middleware('can:change-campaigns');
        Route::post('/drops/add-groups', 'DeploymentController@saveGroups')->middleware('can:change-campaigns');
        Route::post('/drop/{drop}/send-sms/{recipient}', 'DeploymentController@deploySms')->middleware('can:admin-only');
        Route::get('/drop/{drop}/edit', 'DeploymentController@updateForm')->middleware('can:change-campaigns');
        Route::get('/responses', 'ResponseController@getCampaignResponses')->middleware('can:view-campaigns');
        Route::get('/responses/export-responders', 'ResponseController@getAllResponders')->middleware('can:view-campaigns');
        Route::get('/responses/export-nonresponders', 'ResponseController@getNonResponders')->middleware('can:view-campaigns');
        Route::any('/get-responses-hash', 'ResponseController@getResponsesHash')->middleware('can:view-console');
        Route::any('/responses/{recipient}/get-text-hash', 'ResponseController@getTextHash')->middleware('can:view-console');
        Route::any('/responses/{recipient}/get-email-hash', 'ResponseController@getEmailHash')->middleware('can:view-console');
        Route::any('/responses/{recipient}/get-text-thread', 'ResponseController@getTextThread')->middleware('can:view-console');
        Route::any('/responses/{recipient}/get-email-thread', 'ResponseController@getEmailThread')->middleware('can:view-console');
        Route::any('/get-response-list', 'ResponseController@getResponseList')->middleware('can:view-console');
        Route::get('/response/{recipient}', 'ResponseController@getResponse')->middleware('can:view-console');
        Route::post('/text-response/{recipient}', 'ResponseConsoleController@smsReply')->middleware('can:respond-console');
        Route::post('/email-response/{recipient}', 'ResponseConsoleController@emailReply')->middleware('can:respond-console');
        Route::get('/response-console', 'ResponseConsoleController@show')->middleware('can:view-console');
        Route::get('/response-console/unread', 'ResponseConsoleController@showUnread')->middleware('can:view-console');
        Route::get('/response-console/idle', 'ResponseConsoleController@showIdle')->middleware('can:view-console');
        Route::get('/response-console/archived', 'ResponseConsoleController@showArchived')->middleware('can:view-console');
        Route::get('/response-console/labelled/{label}', 'ResponseConsoleController@showLabelled')->middleware('can:view-console');
        Route::get('/response-console/calls', 'ResponseConsoleController@showCalls')->middleware('can:view-console');
        Route::get('/response-console/sms', 'ResponseConsoleController@showTexts')->middleware('can:view-console');
        Route::get('/response-console/email', 'ResponseConsoleController@showEmails')->middleware('can:view-console');
    });

    Route::group(['prefix' => '/recipient/{recipient}', 'middleware' => 'can:change-console'], function () {
        Route::post('/add-label', 'RecipientController@addLabel');
        Route::post('/remove-label', 'RecipientController@removeLabel');
        Route::post('/update-notes', 'RecipientController@updateNotes');
    });

    /* DASHBOARDS */
//    Route::get('/dashboard', 'HomeController@index');
//    Route::get('/lightDashboard', 'HomeController@dashboard');

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

        Route::group(['prefix' => '/{company}/user'], function () {
            Route::get('', 'CompanyController@userIndex')->middleware('can:manage,company')->name('company.user.index');
            Route::get('/create', 'CompanyController@userCreate')->middleware('can:manage,company')->name('company.user.create');
            Route::post('', 'CompanyController@userStore')->middleware('can:manage,company')->name('company.user.store');
            Route::get('/{user}/edit', 'CompanyController@userEdit')->middleware('can:manage,company')->name('company.user.edit');
        });
        Route::get('/{company}/user', 'CompanyController@userIndex')->middleware('can:manage,company')->name('company.user.index');
        Route::get('/{company}/user/create', 'CompanyController@userCreate')->middleware('can:manage,company')->name('company.user.create');
        Route::get('/{company}/user/{user}/edit', 'CompanyController@userEdit')->middleware('can:manage,company')->name('company.user.edit');
        Route::post('/{company}/user', 'CompanyController@userStore')->middleware('can:manage,company')->name('company.user.store');
        Route::get('/{company}/campaign', 'CompanyController@campaignIndex')->middleware('can:viewForPreferences,company')->name('company.campaign.index');
        Route::get('/{company}/edit', 'CompanyController@edit')->middleware('can:manage,company')->name('company.edit');
    });
    //endregion
});
//endregion

//region NEW ROUTES
Route::group(['prefix' => 'registration'], function () {
    Route::get('/complete', 'Auth\CompleteController@show')->middleware('signed', 'justinvited')->name('registration.complete.show');
    Route::post('/complete', 'Auth\CompleteController@set')->middleware('signed')->name('registration.complete.store');
});
//
Route::get('/company/{company}', 'HomeController@companytest')->middleware('company');
//Route::resource('/companies', 'CompanyController')->middleware('can:create,App\Models\Company');

//Route::get('/companies/{company}/dashboard', 'CompanyController@dashboard')->middleware('can:view,company')->name('companies.dashboard');
Route::get('/companies/{company}/preferences', 'CompanyController@preferences')->middleware('can:viewforpreferences,company')->name('companies.preferences');
Route::post('/companies/{company}/preferences', 'CompanyController@setpreferences')->middleware('can:viewforpreferences,company')->name('companies.setpreferences');

Route::get('/companies/{company}/adduser', 'CompanyController@createuser')->middleware('can:manage,company')->name('companies.createuser');
Route::post('/companies/{company}/adduser', 'CompanyController@storeuser')->middleware('can:manage,company')->name('companies.storeuser');

Route::get('/companies/{company}/campaign/{campaign}', 'CompanyController@campaignaccess')->middleware('can:manage,campaign')->name('companies.campaignaccess');
Route::post('/companies/{company}/campaign/{campaign}', 'CompanyController@setcampaignaccess')->middleware('can:manage,campaign')->name('companies.setcampaignaccess');

Route::get('/companies/{company}/user/{user}', 'CompanyController@useraccess')->middleware('can:manage,company')->name('companies.useraccess');
Route::post('/companies/{company}/user/{user}', 'CompanyController@setuseraccess')->middleware('can:manage,company')->name('companies.setuseraccess');

Route::resource('/users', 'UserController')->middleware('can:create,App\Models\User');
Route::get('/impersonateas/{user}', 'Auth\ImpersonateController@login')->middleware('can:create,App\Models\User')->name('auth.impersonate');
Route::get('/leaveimpersonating', 'Auth\ImpersonateController@leave')->name('auth.leaveimpersonate');
Route::resource('/campaigns', 'CampaignController')->middleware('can:create,App\Models\Campaign');
//endregion

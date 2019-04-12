<?php

Route::impersonate();

use Illuminate\Support\Facades\Route;

// AWS heartbeat
Route::get('/heartbeat', function () {
    return response()->json('ok', 200);
});
//region OUTSIDE API CALLS
Route::any('/text-in/', 'TextInController@createFromSms')->name('pub-api.text-in')->middleware(null);
Route::any('/text-responses/inbound', 'ResponseConsoleController@inboundText')->name('pub-api.text-response-inbound')->middleware(null);
Route::any('/email-responses/inbound', 'ResponseConsoleController@inboundEmail')->name('pub-api.email-response-inbound')->middleware(null);
Route::any('/email-responses/log', 'ResponseConsoleController@logEmail')->name('pub-api.email-response-log')->middleware(null);
Route::any('/phone-responses/inbound', 'ResponseConsoleController@inboundPhone')->name('pub-api.phone-response-inbound')->middleware(null);
Route::any('/phone-responses/status', 'ResponseConsoleController@inboundPhoneStatus')->name('pub-api.phone-response-status')->middleware(null);

Route::any('/appointments/insert', 'AppointmentController@insert')->name('pub-api.appointment-insert')->middleware(null);
Route::any('/appointments/save', 'AppointmentController@save')->name('pub-api.appointment-save')->middleware(null);
Route::any('/appointments/get', 'AppointmentController@get')->name('pub-api.appointment-get')->middleware(null);
//endregion

//region AUTH
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('auth.authenticate');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.restore');
//endregion

//region AUTHENTICATED REQUESTS ONLY
Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', 'HomeController@index')->middleware('check.active.company')->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::post('/appointment/{appointment}/update-called-status', 'AppointmentController@updateCalledStatus')->name('appointment.update-called-status');
    Route::post('/callback/{appointment}/update-called-status', 'AppointmentController@updateCalledStatus')->name('callback.update-called-status');

    //region PROFILE
    Route::group(['prefix' => 'profile'], function () {
        Route::get('', 'UserController@view')->name('profile.index');
    });
    //endregion

    //region ADMIN
    Route::group(['prefix' => 'admin'], function () {
        Route::get('/resend-invitation', 'AdminController@resendInvitation')->name('admin.resend-invitation')->middleware('can:resend-invitation,App\Models\User');
        Route::get('/impersonate/leave', 'AdminController@impersonateLeave')->name('admin.impersonate-leave');
        Route::get('/impersonate/{user}', 'AdminController@impersonateUser')->name('admin.impersonate')->middleware('can:impersonate,App\Models\User');
    });
    //endregion

    //region APPOINTMENT
    Route::group(['prefix' => 'appointment'], function () {
        Route::get('for-calendar-display', 'AppointmentController@getForCalendarDisplay')->name('appointment.for-calendar-display');
    });
    //endregion

    //region DROP
    Route::group(['prefix' => 'drop'], function () {
        Route::get('for-calendar-display', 'DropController@getForCalendarDisplay')->name('drop.for-calendar-display');
    });
    //endregion

    //region SELECTOR
    Route::group(['prefix' => 'selector'], function () {
        Route::get('', 'SelectorController@show')->name('selector.select-active-company');
        Route::post('', 'SelectorController@updateActiveCompany')->name('selector.update-active-company');
    });
    //endregion

    //region PHONEVALIDATION
    Route::group(['prefix' => 'phone-verification'], function () {
        Route::any('send-code', 'PhoneVerificationController@sendVerificationCode')->middleware('can:change-campaigns')->name('phone-verification.send-code');
        Route::any('verify-code', 'PhoneVerificationController@verify')->middleware('can:change-campaigns')->name('phone-verification.verify-code');
    });
    //endregion

    //region USER
    Route::group(['prefix' => 'user'], function () {
        Route::get('', 'UserController@index')->name('user.index')->middleware([ 'can:list,App\Models\User']);
        Route::get('for-user-display', 'UserController@getForUserDisplay')->middleware([ 'can:list,App\Models\User'])->name('user.for-user-display');
        Route::get('/create', 'UserController@create')->name('user.create')->middleware([ 'can:create-user,App\Models\User']);
        Route::get('{user}', 'UserController@view')->name('user.view')->middleware([ 'can:list,App\Models\User']);
        Route::get('/{user}/edit', 'UserController@edit')->name('user.edit')->middleware([ 'can:edit-user,user']);
        Route::get('/{user}/activate', 'UserController@activate')->name('user.activate')->middleware([ 'can:edit-user,user']);
        Route::get('/{user}/deactivate', 'UserController@deactivate')->name('user.deactivate')->middleware([ 'can:edit-user,user']);
        Route::post('', 'UserController@store')->name('user.store')->middleware([ 'can:create-user,App\Models\User']);
        Route::post('{user}', 'UserController@update')->name('user.update')->middleware([ 'can:edit-user,user']);
        Route::post('{user}/avatar', 'UserController@updateAvatar')->name('user.update-avatar')->middleware([ 'can:edit-user,user']);
        Route::post('{user}/update-password', 'UserController@updatePassword')->name('user.update-password')->middleware([ 'can:edit-user,user']);
        Route::post('{user}/company-data', 'UserController@updateCompanyData')->name('user.update-company-data')->middleware([ 'can:edit-user,user']);
        Route::delete('/{user}', 'UserController@delete')->name('user.delete')->middleware([ 'can:delete-user,App\Models\User']);
    });
    //endregion

    //region TEMPLATES
    Route::get('/templates', 'TemplateController@index')->name('template.index')->middleware('can:view-templates');
    Route::get('/templates/for-user-display', 'TemplateController@getForUserDisplay')->name('template.for-user-display')->middleware('can:view-templates');
    Route::group(['prefix' => 'template'], function () {
        Route::get('', 'TemplateController@index')->name('template.index')->middleware('can:view-templates');
        Route::get('/create-form', 'TemplateController@createForm')->name('template.create-form')->middleware('can:change-templates');
        Route::post('/create', 'TemplateController@create')->name('template.create')->middleware('can:change-templates');
        Route::group(['prefix' => '{template}', 'middleware' => 'can:view-templates'], function () {
            Route::get('/', 'TemplateController@show')->name('template.show');
            Route::get('/json', 'TemplateController@showJson')->name('templates.show-json');
            Route::get('/edit', 'TemplateController@editForm')->name('template.edit')->middleware('can:change-templates');
            Route::patch('/update', 'TemplateController@update')->name('template.update')->middleware('can:change-templates');
            Route::delete('/delete', 'TemplateController@delete')->name('template.delete')->middleware('can:change-templates');
        });
    });
    //endregion

    //region TEMPLATE BUILDER
    Route::group(['prefix' => 'template-builder', 'middleware' => 'can:admin-only'], function () {
        Route::get('', 'TemplateBuildController@index')->name('template-builder.index');
        Route::get('editor', 'TemplateBuildController@showEditor')->name('template-builder.show-editor');
        Route::get('templates/{template}/{templateName}', 'TemplateBuildController@getTemplate')->name('template-builder.get-template');
        Route::get('upload', 'TemplateBuildController@getImageList')->name('template-builder.get-image-list');
        Route::post('upload', 'TemplateBuildController@uploadImage')->name('template-builder.upload-image');
        Route::get('img', 'TemplateBuildController@getImage')->name('template-builder.get-image');
        Route::get('templates/{template}/edres/{file}', 'TemplateBuildController@getEdresFile')->name('template-builder.get-edres-file');
        Route::post('dl', 'TemplateBuildController@download')->name('template-builder.download-post');
        Route::get('dl', 'TemplateBuildController@download')->name('template-builder.download-get');
        Route::post('create', 'TemplateBuildController@createTemplate')->name('template-builder.store');
    });
    //endregion

    //region CAMPAIGN
    Route::get('/campaigns', 'CampaignController@index')->name('campaigns.index')->middleware('can:view-campaigns');
    Route::get('/campaigns/for-user-display', 'CampaignController@getForUserDisplay')->name('campaign.for-user-display');
    Route::get('/campaigns/user/{user}', 'CampaignController@getUserCampaigns')->name('campaign.user.show')->middleware('can:view-campaigns');
    Route::get('/campaigns/new', 'CampaignController@createNew')->name('campaigns.create')->middleware('can:change-campaigns');
    Route::post('/campaigns/create', 'CampaignController@create')->middleware('can:change-campaigns')->name('campaigns.store');
    Route::group(['prefix' => '/campaign/{campaign}', 'middleware' => ['can:view,campaign']], function () {
        Route::post('/text-response/{recipient}', 'ResponseConsoleController@smsReply')->name('campaign.recipient.text-response');
        Route::post('/email-response/{recipient}', 'ResponseConsoleController@emailReply')->name('campaign.recipient.email-response');
        Route::any('/responses/{recipient}/add-appointment', 'AppointmentController@addAppointmentFromConsole')->name('add-appointment');
        Route::group(['middleware' => ['can:site-admin,App\Models\User']], function () {
            Route::post('/user-access/{user}', 'CampaignController@toggleCampaignUserAccess')->name('campaigns.toggle-user-access');
            Route::get('/stats', 'CampaignController@showStats')->name('campaigns.stats');
            Route::get('/', 'CampaignController@show')->name('campaigns.view');
            Route::delete('/', 'CampaignController@delete');
            Route::get('/details', 'CampaignController@details');
            Route::get('/edit', 'CampaignController@edit')->name('campaigns.edit');
            Route::get('/delete', 'CampaignController@delete')->name('campaigns.delete');
            Route::post('/update', 'CampaignController@update')->name('campaigns.update');
            // Recipient list
            Route::get('/recipient-lists', 'RecipientController@show')->name('campaigns.recipient-lists.index');
            Route::get('/recipient-list/for-user-display', 'RecipientController@forUserDisplay')->name('campaigns.recipient-lists.for-user-display');
            Route::post('/recipient-list/upload', 'RecipientController@uploadFile')->name('campaigns.recipient-lists.upload');
            Route::get('/recipient-list/{list}', 'RecipientController@showRecipientList')->name('campaigns.recipient-lists.show');
            Route::get('/recipient-list/{list}/download', 'RecipientController@downloadRecipientList')->name('campaigns.recipient-lists.download');
            Route::get('/recipient-list/{list}/for-user-display', 'RecipientController@getRecipientsForUserDisplay')->name('campaigns.recipient-lists.recipients.for-user-display');
            Route::delete('/recipient-lists/{list}', 'RecipientController@deleteRecipientList')->name('campaigns.recipient-lists.delete');
            Route::get('/recipient-list/{list}/delete-stats', 'RecipientController@recipientListDeleteStats')->name('campaigns.recipient-lists.delete-stats');
            Route::post('/recipient-list/from-campaign', 'RecipientController@fromCampaign');
            Route::post('/recipient-lists', 'RecipientController@createRecipientList')->name('campaigns.recipient-lists.store');
            // Recipients
            Route::post('/add-recipient', 'RecipientController@add');
            Route::delete('/remove-recipient', 'RecipientController@delete')->name('campaigns.recipients.delete');
            Route::put('/update-recipient', 'RecipientController@update');
            Route::get('/recipients/partialByField', 'RecipientController@getPartialRecipientsByField');
            Route::post('/recipients/deletePartialByField', 'RecipientController@deletePartialRecipientsByField');
            Route::get('/recipients/search', 'RecipientController@searchForDeployment')->name('campaigns.recipients.search');
            Route::any('/recipients/finalize_upload', 'RecipientController@finishUpload');
            Route::get('/recipients/download', 'RecipientController@download');
            Route::get('/recipients/delete-all', 'RecipientController@deleteAll');
            // End of Recipient list pages
            Route::get('phones', 'PhoneController@forCampaign')->middleware('can:modify-campaigns')->name('phone.list');
            Route::get('phone-list-json', 'PhoneController@fromCampaignAsJson')->middleware('can:view-campaigns');
            Route::patch('phone/{phone}', 'PhoneController@store')->middleware('can:change-campaigns')->name('phone.store');
            Route::post('phone/{phone}/release', 'PhoneController@release')->middleware('can:change-campaigns')->name('phone-number.release');
            // Drops
            Route::get('/drops', 'DeploymentController@forCampaign')->name('campaigns.drops.index');
            Route::get('/drops/for-user-display', 'DeploymentController@getForUserDisplay')->name('campaigns.drops.for-user-display');
            Route::get('/drop/{drop}', 'DeploymentController@show')->name('campaigns.drops.details');
            Route::delete('/drop/{drop}', 'DeploymentController@delete')->name('campaigns.drops.delete');
            Route::post('/drop/{drop}/update', 'DeploymentController@update')->name('campaigns.drops.update');
            Route::get('/drops/new', 'DeploymentController@createNew')->name('campaigns.drops.create');
            // Deployment group
            Route::get('/mailer/new', 'DeploymentController@createNewMailer')->name('campaigns.mailer.create');
            Route::post('/mailer', 'DeploymentController@storeMailer')->name('campaigns.drops.store-mailer');
            Route::post('/drops', 'DeploymentController@create')->name('campaigns.drops.store');
            Route::post('/drops/add-groups', 'DeploymentController@saveGroups')->name('campaigns.drops.add-groups');
            Route::post('/drop/{drop}/send-sms/{recipient}', 'DeploymentController@deploySms')->name('campaigns.drops.send-sms');
            Route::get('/drop/{drop}/edit', 'DeploymentController@updateForm')->name('campaigns.drops.edit');
            Route::get('/responses', 'ResponseController@getCampaignResponses')->name('campaigns.responses.index');
            Route::get('/responses/export-responders', 'ResponseController@getAllResponders');
            Route::get('/responses/export-nonresponders', 'ResponseController@getNonResponders');
            Route::any('/get-responses-hash', 'ResponseController@getResponsesHash');
            Route::any('/responses/{recipient}/get-text-hash', 'ResponseController@getTextHash');
            // TODO: remove me from this routes group
            // Route::any('/responses/{recipient}/add-appointment', 'AppointmentController@addAppointmentFromConsole')->middleware('can:view-console')->name('add-appointment');
            Route::any('/responses/{recipient}/get-email-hash', 'ResponseController@getEmailHash');
            Route::any('/responses/{recipient}/get-text-thread', 'ResponseController@getTextThread');
            Route::any('/responses/{recipient}/get-email-thread', 'ResponseController@getEmailThread');
            Route::any('/get-response-list', 'ResponseController@getResponseList');
            // TODO: remove me from this routes group
            // Route::get('/response/{recipient}', 'ResponseController@getResponse')->name('campaign.recipient.responses');
        });

        Route::get('/response/{recipient}', 'ResponseController@getResponse')->name('campaign.recipient.responses');
        Route::any('/responses/{recipient}/add-appointment', 'AppointmentController@addAppointmentFromConsole')->name('add-appointment');

        Route::get('/recipients/for-user-display', 'RecipientController@getRecipients')->name('campaign.recipient.for-user-display');
        Route::get('/response-console/{field?}', 'ResponseConsoleController@show')->name('campaign.response-console.index');
    });
    //endregion

    //region RECIPIENT
    Route::group(['prefix' => '/recipient/{recipient}', 'middleware' => ['can:update,recipient']], function () {
        Route::post('/send-to-crm', 'RecipientController@sendToCrm')->name('recipient.send-to-crm');
        Route::post('/add-label', 'RecipientController@addLabel')->name('recipient.add-label');
        Route::post('/remove-label', 'RecipientController@removeLabel')->name('recipient.remove-label');
        Route::post('/update-notes', 'RecipientController@updateNotes')->name('recipient.update-notes');
        Route::get('/get-responses-by-recipient', 'RecipientController@fetchResponsesByRecipient')->name('recipient.get-responses');
    });
    //endregion

    //region DEPLOYMENT
    /* TODO: change nomenclature to Drops */
    Route::group(['prefix' => '/drop/{deployment}', 'middleware' => 'can:change-campaigns'], function () {
        Route::get('/start', 'DeploymentController@start')->name('deployments.start');
        Route::get('/pause', 'DeploymentController@pause')->name('deployment.pause');
        Route::get('/resume', 'DeploymentController@resume')->name('deployment.resume');
        Route::post('/delete', 'DeploymentController@delete')->name('deployment.delete');
    });
    //endregion

    //region PHONES
    Route::group(['prefix' => '/phones', 'middleware' => 'can:change-campaigns'], function () {
        Route::post('search', 'PhoneController@searchAvailable')->name('phone.search');
        Route::post('provision', 'PhoneController@provision')->name('phone.provision');
        // Route::get('list-unused', 'PhoneController@showUnused'); // Future improvement
        // Route::post('release', 'PhoneController@releaseNumber'); // Future improvement
    });
    //endregion

    //region RESPONSE
    Route::post('/response/{response}/update-read-status', 'ResponseController@updateReadStatus')->name('response.update-read-status');
    //endregion

    //region SYSTEM
    Route::group(['prefix' => 'system', 'middleware' => 'can:admin-only'], function () {
        Route::get('drops', 'SystemController@index')->name('system.drop.index');
        Route::get('reports', 'SystemController@index')->name('system.report.index');
    });
    //endregion

    //region COMPANIES
    Route::group(['prefix' => 'companies'], function () {
        Route::get('/', 'CompanyController@index')->middleware('can:create')->name('company.index');
        Route::post('/', 'CompanyController@store')->middleware('can:create,App\Models\Company')->name('company.store');
        Route::post('{company}/avatar', 'CompanyController@updateAvatar')->name('companies.update-avatar');

        Route::get('for-dropdown', 'CompanyController@getForDropdown')->name('company.for-dropdown');
        Route::get('for-user-display', 'CompanyController@getForUserDisplay')->name('company.for-user-display');
        Route::get('/create', 'CompanyController@create')->middleware('can:create,App\Models\Company')->name('company.create');

        Route::get('/{company}', 'CompanyController@details')->middleware('can:manage,company')->name('company.details');
        Route::put('/{company}', 'CompanyController@update')->middleware('can:edit,company')->name('company.update');

        Route::get('/{company}/campaign', 'CompanyController@campaignIndex')->middleware('can:viewForPreferences,company')->name('company.campaign.index');
        Route::delete('/{company}', 'CompanyController@delete')->middleware('can:manage,company')->name('company.delete');

        Route::group(['prefix' => '/{company}/user'], function () {
            Route::get('', 'CompanyController@userIndex')->middleware('can:manage,company')->name('company.user.index');
            Route::get('/create', 'CompanyController@userCreate')->middleware('can:manage,company')->name('company.user.create');
            Route::post('', 'CompanyController@userStore')->middleware('can:manage,company')->name('company.user.store');
            Route::get('/{user}/edit', 'CompanyController@userEdit')->middleware('can:manage,company')->name('company.user.edit');
            Route::post('/{user}', 'CompanyController@userUpdate')->middleware('can:manage,company')->name('company.user.update');
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

<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|himanshu test
*/
// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::get('/', 'HomeController@index');

Auth::routes();
Route::get('/autologin/{id}', 'Auth\LoginController@autologin')->name('autologin')->middleware('FrameHeadersMiddleware');
Route::post('login', 'Auth\LoginController@authenticate')->name('Manuallogin')->middleware('EventLogRoute');
Route::get('/home', 'HomeController@index')->name('home')->middleware('EventLogRoute');
Route::get('/iframe', 'HomeController@iframe')->name('iframe');
Route::post('/home/getDashboard', 'HomeController@getDashboard')->middleware('EventLogRoute');
Route::get('/home/chk', 'HomeController@chk');
Route::get('register_page', 'Auth\RegisterController@showsignup')->name('signup');
Route::get('register', 'Auth\RegisterController@index')->name('register');
Route::post('FreePullTable','HomeController@FreePullTable')->name('FreePullTable');

//Route  send otp
Route::get('OptPage/{email}','Auth\RegisterController@OptPage')->name('OptPage');
Route::post('storeotp','Auth\RegisterController@store')->name('storeotp');
Route::post('verifyotp','Auth\RegisterController@validateOtp')->name('verifyotp');
Route::get('resend_otp/{email}','Auth\RegisterController@ResendOtpPage')->name('resend_otp');
Route::post('updateotp/{email}','Auth\RegisterController@updateotp')->name('updateotp');

//Route Invite user
Route::post('storeuser','InviteUserController@store')->name('storeuser');



Route::get('home/getPHMReport/{folder}/{id}', 'HomeController@getPHMReport');
Route::post('/home/deleteLookerUser', 'HomeController@deleteLookerUser');
Route::get('/home/{id}/{primary_folder_id}','HomeController@processDashboard');
Route::get('home/phmReport/show/{id}','HomeController@showPhmReport');


//Route::resource('users', 'UsersController');
Route::get('users/add','UsersController@create')->middleware('EventLogRoute');
Route::post('users/store','UsersController@store')->name('storeUser')->middleware('EventLogRoute');
Route::get('users','UsersController@index')->name('users')->middleware('EventLogRoute');
Route::get('users/edit/{id}','UsersController@edit')->middleware('EventLogRoute');
Route::post('users/edit1/{id}','UsersController@update')->name('updateUser')->middleware('EventLogRoute');
Route::delete('users/delet{id}','UsersController@destroy')->name('deleteUser')->middleware('EventLogRoute');
Route::delete('users/terminate/{id}','UsersController@terminate')->name('terminateSession')->middleware('EventLogRoute');
Route::get('users/profile/{id}','UsersController@profile')->name('profile')->middleware('EventLogRoute');
Route::get('users/profileEdit/{id}','UsersController@profile_edit')->name('profileEdit')->middleware('EventLogRoute');
Route::post('users/profileUpdate/{id}','UsersController@updateProfile')->name('updateProfile')->middleware('EventLogRoute');

//Route::post('/users/userlist','UsersController@userlist')->name('userlist');

//looker settings
Route::get('/lookerSetting', 'LookerController@index')->name('lookerSetting')->middleware('EventLogRoute');
Route::post('lookerSetting/edit/{id}','LookerController@update')->name('updateLooker')->middleware('EventLogRoute');

//MATILLION ROUTES
Route::get('elt','ELTController@index');
Route::get('elt/jobs/{proj}','ELTController@jobs')->name('jobs');
Route::get('elt/scheduler/{proj}/{job}','ELTController@scheduler')->name('scheduler');
Route::post('elt/scheduler/rescheduleJob','ELTController@rescheduleJob')->name('rescheduleJob');
Route::get('elt/tasklog/{proj}/{job}','ELTController@tasklog')->name('tasklog');
Route::get('elt/tasklog/deep_dive/{projectName}/{id}','ELTController@deep_dive')->name('deep_dive');

//SNOWFLAKE ROUTES
Route::get('snowflake','SnowflakeController@index');
Route::get('snowflake/dataprocessing','SnowflakeController@dataprocessing')->name('dataprocessing');
Route::post('getyr', 'SnowflakeController@get_base_years');
Route::post('snowflake/execute', 'SnowflakeController@execute')->name('execute');
// Route::get('snowflake/dataProcessing/{proj}','ELTController@jobs')->name('jobs');

//PHM report tttt
Route::get('reports','phmController@index')->name('reports')->middleware('EventLogRoute');
Route::get('reports/add','phmController@create')->middleware('EventLogRoute');
Route::post('reports/getFolder','phmController@getFolder')->middleware('EventLogRoute');
Route::post('getFolder','phmController@getFolder')->middleware('EventLogRoute');
Route::post('phm/store','phmController@store')->name('storePhm')->middleware('EventLogRoute');
Route::delete('phm/delet{id}','phmController@destroy')->name('deletePhm')->middleware('EventLogRoute');
Route::get('reports/edit/{id}','phmController@edit')->middleware('EventLogRoute');

Route::get('reports/copy/{id}','phmController@copy')->middleware('EventLogRoute');
Route::get('reports/canned/{id}','phmController@canned')->middleware('EventLogRoute');
Route::get('reports/downloadWord/{id}','phmController@downloadPDF')->middleware('EventLogRoute');
Route::get('phm/addSectionBelow/{id}/{section_id}','phmController@addSectionBelow')->middleware('EventLogRoute');
Route::get('phm/addSubSectionBelow/{id}/{section_id}/{sub_section_id}','phmController@addSubSectionBelow')->middleware('EventLogRoute');
Route::post('phm/edit1/{id}','phmController@update')->name('updatePhm')->middleware('EventLogRoute');
Route::post('phm/copy1/{id}','phmController@saveCopy')->name('copyPhm')->middleware('EventLogRoute');
Route::post('phm/canned1/{id}','phmController@saveCanned')->name('cannedPhm')->middleware('EventLogRoute');
Route::post('/phm/removeSection', 'phmController@removeSection')->middleware('EventLogRoute');
Route::post('/phm/removeSubSection', 'phmController@removeSubSection')->middleware('EventLogRoute');
Route::post('/phm/markMaster', 'phmController@markMaster')->middleware('EventLogRoute');
Route::post('phm/uploaddoc','phmController@uploadDoc')->name('uploadDoc')->middleware('EventLogRoute');
Route::get('phm/DownloadFormattedDoc/{folder}/{id}','phmController@download_formatted_copy')->name('DownloadFormattedDoc')->middleware('EventLogRoute');

Route::get('roles','RolesController@index')->name('roles')->middleware('EventLogRoute');
Route::get('roles/add','RolesController@create')->middleware('EventLogRoute');
Route::post('roles/store','RolesController@store')->name('storeRole')->middleware('EventLogRoute');
Route::get('roles/edit/{id}','RolesController@edit')->middleware('EventLogRoute');
Route::post('roles/edit1/{id}','RolesController@update')->name('updateRole')->middleware('EventLogRoute');
Route::delete('roles/delet{id}','RolesController@destroy')->name('deleteRole')->middleware('EventLogRoute');

Route::get('clients','ClientController@index')->name('clients')->middleware('EventLogRoute');
Route::get('clients/add','ClientController@create')->middleware('EventLogRoute');
Route::post('clients/store','ClientController@store')->name('storeClient')->middleware('EventLogRoute');
Route::get('clients/edit/{id}','ClientController@edit')->middleware('EventLogRoute');
Route::post('clients/edit1/{id}','ClientController@update')->name('updateClient')->middleware('EventLogRoute');
Route::delete('clients/delet{id}','ClientController@destroy')->name('deleteClient')->middleware('EventLogRoute');

Route::get('/reload-captcha', 'CaptchaServiceController@reloadCaptcha');

Route::get('processing','ProcessingController@index')->name('processing');
Route::post('/processing/getColumn', 'ProcessingController@getColumn');
Route::get('/processing/getColumn', 'ProcessingController@getColumn');
Route::post('/processing/getsingletblColumn', 'ProcessingController@getsingletblColumn');


// Group Routes
Route::get('groups','GroupController@index')->name('groups')->middleware('EventLogRoute');
Route::get('groups/add','GroupController@create')->middleware('EventLogRoute');
Route::post('groups/store','GroupController@store')->name('storeGroup')->middleware('EventLogRoute');
Route::get('groups/edit/{id}','GroupController@edit')->middleware('EventLogRoute');
Route::post('groups/edit1/{id}','GroupController@update')->name('updateGroup')->middleware('EventLogRoute');
Route::delete('groups/delet{id}','GroupController@destroy')->name('deleteGroup')->middleware('EventLogRoute');
Route::post('groups/getRoleDetails','GroupController@getRoleDetails')->middleware('EventLogRoute');
Route::post('groups/getGroupRoleDetails','GroupController@getGroupRoleDetails')->middleware('EventLogRoute');
Route::get('groups/getGroup','GroupController@getGroup')->middleware('EventLogRoute');


// Group Master Routes
Route::get('group_master','GroupMasterController@index')->name('group_master')->middleware('EventLogRoute');
Route::get('group_master/add','GroupMasterController@create')->middleware('EventLogRoute');
Route::post('group_master/store','GroupMasterController@store')->name('storeGroupMaster')->middleware('EventLogRoute');
Route::get('group_master/edit/{id}/{role_id}','GroupMasterController@edit')->middleware('EventLogRoute');
Route::post('group_master/edit1/{id}/{role_id}','GroupMasterController@update')->name('updateGroupMaster')->middleware('EventLogRoute');
Route::delete('group_master/delet{id}/{role_id}','GroupMasterController@destroy')->name('deleteGroupMaster')->middleware('EventLogRoute');
Route::post('/group_master/checkGroupName', 'GroupMasterController@checkGroupName')->middleware('EventLogRoute');

// GENERATE REPORT ROUTE
Route::get('all_reports','GenerateReportController@index')->name('all_reports')->middleware('EventLogRoute');
Route::get('report/add','GenerateReportController@create')->middleware('EventLogRoute');
Route::get('report/download','GenerateReportController@download');
Route::get('report/d','GenerateReportController@d');
Route::get('report/cropimg','GenerateReportController@cropimg');
Route::post('report/store','GenerateReportController@store')->name('storeReport')->middleware('EventLogRoute');
Route::get('report/view','GenerateReportController@view');
Route::get('report/get_file/{id}','GenerateReportController@get_file')->middleware('EventLogRoute');
Route::get('report/view_report_doc/{id}','GenerateReportController@view_report');
Route::post('/report/update_flag','GenerateReportController@update_flag');
Route::post('/report/get_base_years','GenerateReportController@get_base_years');
Route::post('/report/get_dates','GenerateReportController@get_dates');
Route::delete('report/delete/{id}','GenerateReportController@destroy')->name('deleteReport')->middleware('EventLogRoute');
Route::get('report/downloadPDF/{folder}/{id}','GenerateReportController@download_formatted_pdf')->name('DownlaodPHMPDF')->middleware('EventLogRoute');
Route::get('report/weekly','GenerateReportController@weekly');
Route::get('report/view_weekly','GenerateReportController@view_weekly');
Route::get('report/dd','GenerateReportController@get_dates_range');


//Schedular->Looker Data localization
Route::get('fetchfolder','SchedularController@getFolder')->name('fetchfolder');
Route::get('fetchdash','SchedularController@getdash')->name('fetchdash');
Route::get('parentfolder','SchedularController@parent_folder')->name('parentfolder');
Route::get('parentphm','SchedularController@parent_phm')->name('parentphm');
Route::get('schema','SchedularController@schema')->name('schema');
Route::get('test','SchedularController@test')->name('test');
Route::get('refresh_data','SchedularController@refresh_data')->name('refresh_data');


//Patient Summary Dashboard Download
Route::get('render_dashboard','PatientSummaryPdfController@render_dashboards')->name('render_dashboard');
Route::get('zip','PatientSummaryPdfController@zip')->name('zip');
Route::get('get_render_task/{id}/{name}','PatientSummaryPdfController@get_render_task')->name('get_render_task');
Route::get('get_render_task_result/{id}/{name}','PatientSummaryPdfController@get_render_task_result')->name('get_render_task_result');
Route::get('PatientSummary_Reports','PatientSummaryPdfController@index')->name('PatientSummary_Reports')->middleware('EventLogRoute');
Route::get('PatientSummary/add','PatientSummaryPdfController@create')->middleware('EventLogRoute');
Route::post('PatientSummary/store','PatientSummaryPdfController@store')->name('storePatientSummary')->middleware('EventLogRoute');
Route::delete('PatientSummary/delete/{id}','PatientSummaryPdfController@destroy')->name('deletePatientSummary')->middleware('EventLogRoute');
Route::get('PatientSummary/downloadZip/{folder}/{id}','PatientSummaryPdfController@download_zip')->name('DownlaodZip')->middleware('EventLogRoute');
Route::get('get_list/{id}','PatientSummaryPdfController@list')->name('get_list');
Route::post('/get_mapping','PatientSummaryPdfController@get_mapping');
Route::post('/get_patient','PatientSummaryPdfController@get_patient');
Route::post('/get_patientCount','PatientSummaryPdfController@get_patientCount');
Route::get('patient_count','PatientSummaryPdfController@patient_count')->name('patient_count');

//PASSWORD RESET
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::get('/change-password', function () {
    return view('auth.forgot-password');
})->name('password.change');


Route::post('/reset-pass','ResetPasswordController@reset_mail')->name('sendresetrequest');
Route::post('/update-pass','ResetPasswordController@update_pass')->name('update-password');
Route::get('metabase', 'HomeController@metabase');
//FOR PRODUCTION SSL
URL::forceScheme('https');

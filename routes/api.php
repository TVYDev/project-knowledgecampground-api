<?php

use Illuminate\Http\Request;
use App\Libs\MiddlewareConst;
use App\Libs\RouteConst;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
    ], function() {
        Route::post('login', 'UserController@login')->name(RouteConst::USER_LOGIN);
        Route::post('register', 'UserController@register')->name(RouteConst::USER_REGISTER);
        Route::post('refresh-token', 'UserController@refreshToken')->name(RouteConst::USER_REFRESH_TOKEN);
        Route::post('send-reset-email', 'UserController@postSendResetEmail')->name(RouteConst::USER_POST_SEND_RESET_EMAIL);

        Route::get('user', 'UserController@getUser')->middleware(MiddlewareConst::JWT_AUTH, MiddlewareConst::JWT_CLAIMS)->name(RouteConst::USER_GET_USER);
        Route::get('verify-authentication', 'UserController@verifyAuthentication')->middleware(MiddlewareConst::JWT_AUTH, MiddlewareConst::JWT_CLAIMS)->name(RouteConst::USER_VERIFY_AUTHENTICATION);
        Route::get('user-permissions', 'UserController@getUserPermissions')->middleware(MiddlewareConst::JWT_AUTH, MiddlewareConst::JWT_CLAIMS)->name(RouteConst::USER_GET_USER_PERMISSIONS);

        Route::post('logout', 'UserController@logout')->middleware(MiddlewareConst::JWT_AUTH, MiddlewareConst::JWT_CLAIMS)->name(RouteConst::USER_LOGOUT);
        Route::post('change-password', 'UserController@changePassword')->middleware(MiddlewareConst::JWT_AUTH, MiddlewareConst::JWT_CLAIMS)->name(RouteConst::USER_CHANGE_PASSWORD);
        Route::post('/reset-password', 'UserController@postResetPassword')->middleware(MiddlewareConst::JWT_AUTH, MiddlewareConst::JWT_CLAIMS)->name(RouteConst::USER_POST_RESET_PASSWORD);
});

Route::group([
    'prefix' => 'role'
    ], function() {
        Route::post('create-role', 'RoleController@postCreateRole')->name('role.postCreateRole');
        Route::get('available-roles', 'RoleController@getAvailableRoles')->name('role.getAvailableRoles');
        Route::get('view/{roleId}', 'RoleController@getViewRole')->name('role.getViewRole');
        Route::post('assign-role-to-user', 'RoleController@postAssignRoleToUser')->name('role.postAssignRoleToUser');
        Route::post('remove-role-from-user', 'RoleController@postRemoveRoleFromUser')->name('role.postRemoveRoleFromUser');
});

Route::group([
    'prefix' => 'permission'
    ], function() {
        Route::post('create-permission', 'PermissionController@postCreatePermission')->name('permission.postCreatePermission');
        Route::get('available-permissions', 'PermissionController@getAvailablePermissions')->name('permission.getAvailablePermissions');
        Route::get('view/{permissionId}', 'PermissionController@getViewPermission')->name('permission.getViewPermission');
        Route::post('assign-permissions-to-role', 'PermissionController@postAssignPermissionsToRole')->name('permission.postAssignPermissionsToRole');
        Route::post('remove-permissions-from-role', 'PermissionController@postRemovePermissionsFromRole')->name('permission.postRemovePermissionsFromRole');
});

Route::group([
    'prefix' => 'social-auth'
    ], function() {
        Route::post('/login', 'SocialAuthController@postLogin')->name('socialAuth.postLogin');
});

Route::group([
    'prefix' => 'user-profile'
    ], function() {
        Route::middleware(MiddlewareConst::JWT_AUTH)->post('update', 'UserProfileController@postUpdate')->name('userProfile.postUpdate');
        Route::middleware(MiddlewareConst::JWT_AUTH)->get('view', 'UserProfileController@getView')->name('userProfile.getView');
});

Route::group([
    'prefix' => 'question'
    ], function (){
        Route::post('/save-during-editing', 'QuestionController@postSaveDuringEditing')->name('question.postSaveDuringEditing');
        Route::put('/save/{publicId}', 'QuestionController@putSave')->name('question.putSave');
        Route::get('/view/{publicId}', 'QuestionController@getQuestion')->name('question.getQuestion');
        Route::get('/description-of/{publicId}', 'QuestionController@getDescriptionOfQuestion')->name('question.getDescriptionOfQuestion');
        Route::get('/list', 'QuestionController@getList')->name('question.getList');
        Route::get('/get-subject-tags-of/{publicId}', 'QuestionController@getSubjectTagsOfQuestion')->name('question.getSubjectTagsOfQuestion');
});

Route::group([
   'prefix' => 'answer'
    ], function() {
        Route::post('/save-during-editing', 'AnswerController@postSaveDuringEditing')->name('answer.postSaveDuringEditing');
        Route::put('/save/{publicId}', 'AnswerController@putSave')->name('answer.putSave');
        Route::get('/view/{publicId}', 'AnswerController@getAnswer')->name('answer.getAnswer');
        Route::get('/description-of/{publicId}', 'AnswerController@getDescriptionOfAnswer')->name('answer.getDescriptionOfAnswer');
        Route::get('/list-posted-answers-of/{questionPublicId}', 'AnswerController@getListPostedAnswersOfQuestion')->name('answer.getListPostedAnswersOfQuestion');
});

Route::group([
    'prefix' => 'comment',
    'middleware' => MiddlewareConst::JWT_AUTH
    ], function() {
        Route::post('/save', 'CommentController@postSave')->name('comment.postSave');
//        Route::get('/list-posted-comments-of/{commentableType}/{commentablePublicId}', 'CommentController@getListPostedCommentsOfCommentableModel')
//            ->name('comment.getListPostedCommentsOfCommentableModel');
});

Route::group([
    'prefix' => 'reply',
    'middleware' => MiddlewareConst::JWT_AUTH
    ], function() {
        Route::post('/save', 'ReplyController@postSave')->name('reply.postSave');
});

Route::group([
    'prefix' => 'subject',
    ], function () {
        Route::get('/all-subjects', 'SubjectController@getAllSubjects')->name('subject.getAllSubjects');
});

Route::group([
    'prefix' => 'tag',
    ], function () {
        Route::get('/all-tags-of/{subjectPublicId}', 'TagController@getAllTagsOfSubject')->name('tag.getAllTagsOfSubject');
});

Route::group([
    'prefix' => 'support'
    ], function (){
        Route::get('/generate-public-id', 'SupportController@getGeneratePublicId')->name('support.getGeneratePublicId');
        Route::get('/clear-cache-key-validation-rules', 'SupportController@clearCacheValidationRules')->name('support.clearCacheValidationRules');
});

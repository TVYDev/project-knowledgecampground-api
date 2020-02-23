<?php

use Illuminate\Http\Request;
use App\Libs\MiddlewareConst;

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
        Route::post('login', 'UserController@login')->name('user.login');
        Route::post('register', 'UserController@register')->name('user.register');
        Route::middleware(MiddlewareConst::JWT_AUTH)->post('/logout', 'UserController@logout')->name('user.logout');
        Route::middleware(MiddlewareConst::JWT_AUTH)->get('/user', 'UserController@getUser')->name('user.getUser');
        Route::middleware(MiddlewareConst::JWT_AUTH)->post('/change-password', 'UserController@changePassword')->name('user.changePassword');
        Route::middleware(MiddlewareConst::JWT_AUTH)->get('/verify-authentication', 'UserController@verifyAuthentication')->name('user.verifyAuthentication');
        Route::post('/refresh-token', 'UserController@refreshToken')->name('user.refreshToken');
        Route::middleware(MiddlewareConst::JWT_AUTH)->get('/user-permissions', 'UserController@getUserPermissions')->name('user.getUserPermissions');
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
        Route::post('/google/login', 'SocialAuthController@postGoogleLogin')->name('socialAuth.googleLogin');
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

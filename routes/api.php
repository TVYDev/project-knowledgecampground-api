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
        Route::post('login', 'UserController@postLoginUser')
            ->name(RouteConst::USER_POST_LOGIN_USER);
        Route::post('register', 'UserController@postRegisterUser')
            ->name(RouteConst::USER_POST_REGISTER_USER);
        Route::post('refresh-token', 'UserController@postRefreshAccessToken')
            ->name(RouteConst::USER_POST_REFRESH_ACCESS_TOKEN);
        Route::post('send-reset-email', 'UserController@postSendMailLinkResetUserPassword')
            ->name(RouteConst::USER_POST_SEND_MAIL_LINK_RESET_USER_PASSWORD);
        Route::get('user', 'UserController@getRetrieveAuthenticatedUser')
            ->name(RouteConst::USER_GET_RETRIEVE_AUTHENTICATED_USER);
        Route::get('verify-authentication', 'UserController@getVerifyUserAuthentication')
            ->name(RouteConst::USER_GET_VERIFY_USER_AUTHENTICATION);
        Route::get('user-permissions', 'UserController@getRetrieveUserPermissions')
            ->name(RouteConst::USER_GET_RETRIEVE_USER_PERMISSIONS);
        Route::post('logout', 'UserController@postLogoutUser')
            ->name(RouteConst::USER_POST_LOGOUT_USER);
        Route::post('change-password', 'UserController@postChangeUserPassword')
            ->name(RouteConst::USER_POST_CHANGE_USER_PASSWORD);
        Route::post('reset-password', 'UserController@postResetUserPassword')
            ->name(RouteConst::USER_POST_RESET_USER_PASSWORD);
});

Route::group([
    'prefix' => 'role'
    ], function() {
        Route::post('create-role', 'RoleController@postCreateUserRole')
            ->name(RouteConst::ROLE_POST_CREATE_USER_ROLE);
        Route::get('available-roles', 'RoleController@getRetrieveAvailableUserRoles')
            ->name(RouteConst::ROLE_GET_RETRIEVE_AVAILABLE_USER_ROLES);
        Route::get('view/{roleId}', 'RoleController@getViewUserRole')
            ->name(RouteConst::ROLE_GET_VIEW_USER_ROLE);
        Route::post('assign-role-to-user', 'RoleController@postAssignUserRoleToUser')
            ->name(RouteConst::ROLE_POST_ASSIGN_USER_ROLE_TO_USER);
        Route::post('remove-role-from-user', 'RoleController@postRemoveUserRoleFromUser')
            ->name(RouteConst::ROLE_POST_REMOVE_USER_ROLE_FROM_USER);
});

Route::group([
    'prefix' => 'permission'
    ], function() {
        Route::post('create-permission', 'PermissionController@postCreateUserPermission')
            ->name(RouteConst::PERMISSION_POST_CREATE_USER_PERMISSION);
        Route::get('available-permissions', 'PermissionController@getRetrieveAvailableUserPermissions')
            ->name(RouteConst::PERMISSION_GET_RETRIEVE_AVAILABLE_USER_PERMISSIONS);
        Route::get('view/{permissionId}', 'PermissionController@getViewUserPermission')
            ->name(RouteConst::PERMISSION_GET_VIEW_USER_PERMISSION);
        Route::post('assign-permissions-to-role', 'PermissionController@postAssignUserPermissionsToUserRole')
            ->name(RouteConst::PERMISSION_POST_ASSIGN_USER_PERMISSIONS_TO_USER_ROLE);
        Route::post('remove-permissions-from-role', 'PermissionController@postRemoveUserPermissionsFromUserRole')
            ->name(RouteConst::PERMISSION_POST_REMOVE_USER_PERMISSIONS_FROM_USER_ROLE);
});

Route::group([
    'prefix' => 'social-auth'
    ], function() {
        Route::post('login', 'SocialAuthController@postLoginSocialUser')
            ->name(RouteConst::SOCIAL_AUTH_POST_LOGIN_SOCIAL_USER);
});

Route::group([
    'prefix' => 'user-profile'
    ], function() {
        Route::post('update', 'UserProfileController@postUpdateUserProfile')
            ->name(RouteConst::USER_PROFILE_POST_UPDATE_USER_PROFILE);
        Route::get('view', 'UserProfileController@getViewUserProfile')
            ->name(RouteConst::USER_PROFILE_GET_VIEW_USER_PROFILE);
});

Route::group([
    'prefix' => 'question'
    ], function (){
        Route::post('save-during-editing', 'QuestionController@postSaveQuestionDuringEditing')
            ->name(RouteConst::QUESTION_POST_SAVE_QUESTION_DURING_EDITING);
        Route::put('save/{publicId}', 'QuestionController@putSaveQuestion')
            ->name(RouteConst::QUESTION_PUT_SAVE_QUESTION);
        Route::get('view/{publicId}', 'QuestionController@getViewQuestion')
            ->name(RouteConst::QUESTION_GET_VIEW_QUESTION);
        Route::get('description-of/{publicId}', 'QuestionController@getRetrieveDescriptionOfQuestion')
            ->name(RouteConst::QUESTION_GET_RETRIEVE_DESCRIPTION_OF_QUESTION);
        Route::get('list', 'QuestionController@getRetrieveListOfQuestions')
            ->name(RouteConst::QUESTION_GET_RETRIEVE_LIST_OF_QUESTIONS);
        Route::get('get-subject-tags-of/{publicId}', 'QuestionController@getRetrieveSubjectTagsOfQuestion')
            ->name(RouteConst::QUESTION_GET_RETRIEVE_SUBJECT_TAGS_OF_QUESTION);
});

Route::group([
   'prefix' => 'answer'
    ], function() {
        Route::post('save-during-editing', 'AnswerController@postSaveAnswerDuringEditing')
            ->name(RouteConst::ANSWER_POST_SAVE_ANSWER_DURING_EDITING);
        Route::put('save/{publicId}', 'AnswerController@putSaveAnswer')
            ->name(RouteConst::ANSWER_PUT_SAVE_ANSWER);
        Route::get('view/{publicId}', 'AnswerController@getViewAnswer')
            ->name(RouteConst::ANSWER_GET_VIEW_ANSWER);
        Route::get('description-of/{publicId}', 'AnswerController@getRetrieveDescriptionOfAnswer')
            ->name(RouteConst::ANSWER_GET_RETRIEVE_DESCRIPTION_OF_ANSWER);
        Route::get('list-posted-answers-of/{questionPublicId}', 'AnswerController@getRetrieveListPostedAnswersOfQuestion')
            ->name(RouteConst::ANSWER_GET_RETRIEVE_LIST_POSTED_ANSWERS_OF_QUESTION);
});

Route::group([
    'prefix' => 'comment'
    ], function() {
        Route::post('save', 'CommentController@postSaveComment')
            ->name(RouteConst::COMMENT_POST_SAVE_COMMENT);
        Route::get('list-posted-comments-of/{commentableType}/{commentablePublicId}', 'CommentController@getRetrieveListPostedCommentsOfCommentableModel')
            ->name(RouteConst::COMMENT_GET_RETRIEVE_LIST_POSTED_COMMENTS_OF_COMMENTABLE_MODEL);
});

Route::group([
    'prefix' => 'reply'
    ], function() {
        Route::post('save', 'ReplyController@postSaveReply')
            ->name(RouteConst::REPLY_POST_SAVE_REPLY);
});

Route::group([
    'prefix' => 'subject',
    ], function () {
        Route::get('all-subjects', 'SubjectController@getRetrieveAllSubjects')
            ->name(RouteConst::SUBJECT_GET_RETRIEVE_ALL_SUBJECTS);
});

Route::group([
    'prefix' => 'tag',
    ], function () {
        Route::get('all-tags-of/{subjectPublicId}', 'TagController@getRetrieveAllTagsOfSubject')
            ->name(RouteConst::TAG_GET_RETRIEVE_ALL_TAGS_OF_SUBJECT);
});

Route::group([
    'prefix' => 'support'
    ], function (){
        Route::get('generate-public-id', 'SupportController@getGeneratePublicId')
            ->name(RouteConst::SUPPORT_GET_GENERATE_PUBLIC_ID);
        Route::get('clear-cache-key-validation-rules', 'SupportController@getClearCacheValidationRules')
            ->name(RouteConst::SUPPORT_GET_CLEAR_CACHE_VALIDATION_RULES);
});

Route::group([
    'prefix' => 'activity'
    ], function (){
        Route::get('my-posts', 'ActivityController@getRetrieveMyPosts')
            ->name(RouteConst::ACTIVITY_GET_RETRIEVE_MY_POSTS);
        Route::post('vote-post', 'ActivityController@postVotePost')
            ->name(RouteConst::ACTIVITY_POST_VOTE_POST);
        Route::post('manage-favorite-question', 'ActivityController@postManageFavoriteQuestion')
            ->name(RouteConst::ACTIVITY_POST_MANAGE_FAVORITE_QUESTION);
});

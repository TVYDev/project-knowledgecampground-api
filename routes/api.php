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
});

Route::group([
    'prefix' => 'user-avatar',
    'middleware' => MiddlewareConst::JWT_AUTH
    ], function() {
        Route::get('/user-avatar', 'UserAvatarController@getUserAvatar')->name('userAvatar.getUserAvatar');
});

Route::group([
    'prefix' => 'question',
    'middleware' => MiddlewareConst::JWT_AUTH
    ], function (){
        Route::post('/save-during-editing', 'QuestionController@postSaveDuringEditing')->name('question.postSaveDuringEditing');
        Route::put('/save/{publicId}', 'QuestionController@putSave')->name('question.putSave');
        Route::get('/view/{publicId}', 'QuestionController@getQuestion')->name('question.getQuestion');
        Route::get('/description-of/{publicId}', 'QuestionController@getDescriptionOfQuestion')->name('question.getDescriptionOfQuestion');
});

Route::group([
   'prefix' => 'answer',
   'middleware' => MiddlewareConst::JWT_AUTH
    ], function() {
        Route::post('/save-during-editing', 'AnswerController@postSaveDuringEditing')->name('answer.postSaveDuringEditing');
        Route::put('/save/{publicId}', 'AnswerController@putSave')->name('answer.putSave');
        Route::get('/view/{publicId}', 'AnswerController@getAnswer')->name('answer.getAnswer');
        Route::get('/description-of/{publicId}', 'AnswerController@getDescriptionOfAnswer')->name('answer.getDescriptionOfAnswer');
        Route::get('/list-posted-answers-of/{questionPublicId}/{sortedType}', 'AnswerController@getListPostedAnswersOfQuestion')->name('answer.getListPostedAnswersOfQuestion');
});

Route::group([
    'prefix' => 'comment',
    'middleware' => MiddlewareConst::JWT_AUTH
    ], function() {
        Route::post('/save', 'CommentController@postSave')->name('comment.postSave');
        Route::get('/list-posted-comments-of/{commentableType}/{commentablePublicId}', 'CommentController@getListPostedCommentsOfCommentableModel')
            ->name('comment.getListPostedCommentsOfCommentableModel');
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
    'prefix' => 'support',
    'middleware' => MiddlewareConst::JWT_AUTH
    ], function (){
        Route::get('/generate-public-id', 'SupportController@getGeneratePublicId')->name('support.getGeneratePublicId');
        Route::get('/clear-cache-key-validation-rules', 'SupportController@clearCacheValidationRules')->name('support.clearCacheValidationRules');
});

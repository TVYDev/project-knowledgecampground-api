<?php


namespace App\Libs;


class RouteConst
{
    /**
     * UserController Routes
     */
    const USER_POST_LOGIN_USER = 'user.postLoginUser';
    const USER_POST_REGISTER_USER = 'user.postRegisterUser';
    const USER_POST_REFRESH_ACCESS_TOKEN = 'user.postRefreshAccessToken';
    const USER_POST_SEND_MAIL_LINK_RESET_USER_PASSWORD = 'user.postSendMailLinkResetUserPassword';
    const USER_GET_RETRIEVE_AUTHENTICATED_USER = 'user.getRetrieveAuthenticatedUser';
    const USER_GET_VERIFY_USER_AUTHENTICATION = 'user.getVerifyUserAuthentication';
    const USER_GET_RETRIEVE_USER_PERMISSIONS = 'user.getRetrieveUserPermissions';
    const USER_POST_LOGOUT_USER = 'user.postLogoutUser';
    const USER_POST_CHANGE_USER_PASSWORD = 'user.postChangeUserPassword';
    const USER_POST_RESET_USER_PASSWORD = 'user.postResetUserPassword';

    /**
     * RoleController Routes
     */
    const ROLE_POST_CREATE_USER_ROLE = 'role.postCreateUserRole';
    const ROLE_GET_RETRIEVE_AVAILABLE_USER_ROLES = 'role.getRetrieveAvailableUserRoles';
    const ROLE_GET_VIEW_USER_ROLE = 'role.getViewUserRole';
    const ROLE_POST_ASSIGN_USER_ROLE_TO_USER = 'role.postAssignUserRoleToUser';
    const ROLE_POST_REMOVE_USER_ROLE_FROM_USER = 'role.postRemoveUserRoleFromUser';

    /**
     * PermissionController Routes
     */
    const PERMISSION_POST_CREATE_USER_PERMISSION = 'permission.postCreateUserPermission';
    const PERMISSION_GET_RETRIEVE_AVAILABLE_USER_PERMISSIONS = 'permission.getRetrieveAvailableUserPermissions';
    const PERMISSION_GET_VIEW_USER_PERMISSION = 'permission.getViewUserPermission';
    const PERMISSION_POST_ASSIGN_USER_PERMISSIONS_TO_USER_ROLE = 'permission.postAssignUserPermissionsToUserRole';
    const PERMISSION_POST_REMOVE_USER_PERMISSIONS_FROM_USER_ROLE = 'permission.postRemoveUserPermissionsFromUserRole';

    /**
     * SocialAuthController Routes
     */
    const SOCIAL_AUTH_POST_LOGIN_SOCIAL_USER = 'socialAuth.postLoginSocialUser';

    /**
     * UserProfileController Routes
     */
    const USER_PROFILE_POST_UPDATE_USER_PROFILE = 'userProfile.postUpdateUserProfile';
    const USER_PROFILE_GET_VIEW_USER_PROFILE = 'userProfile.getViewUserProfile';

    /**
     * QuestionController Routes
     */
    const QUESTION_POST_SAVE_QUESTION_DURING_EDITING = 'question.postSaveQuestionDuringEditing';
    const QUESTION_PUT_SAVE_QUESTION = 'question.putSaveQuestion';
    const QUESTION_GET_VIEW_QUESTION = 'question.getViewQuestion';
    const QUESTION_GET_RETRIEVE_DESCRIPTION_OF_QUESTION = 'question.getRetrieveDescriptionOfQuestion';
    const QUESTION_GET_RETRIEVE_LIST_OF_QUESTIONS = 'question.getRetrieveListOfQuestions';
    const QUESTION_GET_RETRIEVE_SUBJECT_TAGS_OF_QUESTION = 'question.getRetrieveSubjectTagsOfQuestion';

    /**
     * AnswerController Routes
     */
    const ANSWER_POST_SAVE_ANSWER_DURING_EDITING = 'answer.postSaveAnswerDuringEditing';
    const ANSWER_PUT_SAVE_ANSWER = 'answer.putSaveAnswer';
    const ANSWER_GET_VIEW_ANSWER = 'answer.getViewAnswer';
    const ANSWER_GET_RETRIEVE_DESCRIPTION_OF_ANSWER = 'answer.getRetrieveDescriptionOfAnswer';
    const ANSWER_GET_RETRIEVE_LIST_POSTED_ANSWERS_OF_QUESTION = 'answer.getRetrieveListPostedAnswersOfQuestion';

    /**
     * CommentController Routes
     */
    const COMMENT_POST_SAVE_COMMENT = 'comment.postSaveComment';
    const COMMENT_GET_RETRIEVE_LIST_POSTED_COMMENTS_OF_COMMENTABLE_MODEL = 'comment.getRetrieveListPostedCommentsOfCommentableModel';

    /**
     * ReplyController Routes
     */
    const REPLY_POST_SAVE_REPLY = 'reply.postSaveReply';

    /**
     * SubjectController Routes
     */
    const SUBJECT_GET_RETRIEVE_ALL_SUBJECTS = 'subject.getRetrieveAllSubjects';

    /**
     * TagController Routes
     */
    const TAG_GET_RETRIEVE_ALL_TAGS_OF_SUBJECT = 'tag.getRetrieveAllTagsOfSubject';

    /**
     * SupportController Routes
     */
    const SUPPORT_GET_GENERATE_PUBLIC_ID = 'support.getGeneratePublicId';
    const SUPPORT_GET_CLEAR_CACHE_VALIDATION_RULES = 'support.getClearCacheValidationRules';

    /**
     * "activity" routes
     */
    const ACTIVITY_GET_RETRIEVE_MY_POSTS = 'activity.getRetrieveMyPosts';
    const ACTIVITY_POST_VOTE_POST = 'activity.postVotePost';
    const ACTIVITY_POST_MANAGE_FAVORITE_QUESTION = 'activity.postManageFavoriteQuestion';
}

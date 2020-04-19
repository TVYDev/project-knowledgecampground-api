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
     * "activity" routes
     */
    const ACTIVITY_GET_MY_POSTS = 'activity.getMyPosts';
}

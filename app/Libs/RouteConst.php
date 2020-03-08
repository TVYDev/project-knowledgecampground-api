<?php


namespace App\Libs;


class RouteConst
{
    /**
     * "auth" routes
     */
    const USER_LOGIN = 'user.login';
    const USER_REGISTER = 'user.register';
    const USER_REFRESH_TOKEN = 'user.refreshToken';
    const USER_POST_SEND_RESET_EMAIL = 'user.postSendResetEmail';
    const USER_GET_USER = 'user.getUser';
    const USER_VERIFY_AUTHENTICATION = 'user.verifyAuthentication';
    const USER_GET_USER_PERMISSIONS = 'user.getUserPermissions';
    const USER_LOGOUT = 'user.logout';
    const USER_CHANGE_PASSWORD = 'user.changePassword';
}

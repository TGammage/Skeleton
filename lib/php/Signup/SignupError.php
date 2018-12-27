<?php

namespace SystemCore\Signup;

class SignupError
{
    /**
     * Constants
     */
    const NO_ERROR              = 1000;
    const MISSING_EMAIL         = 1001;
    const MISSING_USERNAME      = 1002;
    const MISSING_PASSWORD      = 1003;
    const MISSING_CONFIRM       = 1004;
    const EMPTY_EMAIL           = 1005;
    const EMPTY_USERNAME        = 1006;
    const EMPTY_PASSWORD        = 1007;
    const EMPTY_CONFIRM         = 1008;
    const INVALID_EMAIL         = 1009;
    const DUPLICATE_EMAIL       = 1010;
    const DUPLICATE_USERNAME    = 1011;
    const FORMAT_USERNAME       = 1012;
    const FORMAT_FIRST_NAME     = 1013;
    const FORMAT_LAST_NAME      = 1014;
    const FORMAT_PASSWORD       = 1015;
    const CONFIRM_PASSWORD      = 1016;
    //-----Server Visibility Only-----
    const BACKSIDE_ONLY         = 1017;
    const MEMBER_DB_INSERTION   = 1019;
    const MAIN_DB_INSERTION     = 1020;
    const SEND_EMAIL            = 1021;
    const MEMBER_UNLOCK         = 1022;
    //-----Server Visibility Only-----

    /**
     * message()
     *
     * @purpose
     *  Interprets a message code sent to a front side page.
     *  This is to assist in determining a cause of a signup failure
     *
     * @return string Message of generated error
     */
    public static function message( $code )
    {
        switch( $code )
        {
            default : $message = "General Error"; break;

            case self::NO_ERROR             : $message = "Signup successful"; break;
            case self::MISSING_EMAIL        : $message = "Email field not found"; break;
            case self::MISSING_USERNAME     : $message = "Username field not found"; break;
            case self::MISSING_PASSWORD     : $message = "Password field not found"; break;
            case self::MISSING_CONFIRM      : $message = "Password confirmation field not found"; break;
            case self::EMPTY_EMAIL          : $message = "Email required"; break;
            case self::EMPTY_USERNAME       : $message = "Username required"; break;
            case self::EMPTY_PASSWORD       : $message = "Password required"; break;
            case self::EMPTY_CONFIRM        : $message = "Password confirmation required"; break;
            case self::INVALID_EMAIL        : $message = "Email address unaccepted"; break;
            case self::DUPLICATE_EMAIL      : $message = "Email address exists on database"; break;
            case self::DUPLICATE_USERNAME   : $message = "Username exists on database"; break;
            case self::FORMAT_FIRST_NAME    : $message = "First name format unaccepted"; break;
            case self::FORMAT_LAST_NAME     : $message = "Last name format unaccepted"; break;
            case self::FORMAT_PASSWORD      : $message = "Password format unaccepted"; break;
            case self::CONFIRM_PASSWORD     : $message = "Password confirmation mismatch"; break;
            //-----Server Visibility Only-----
            case self::MEMBER_DB_INSERTION  : $message = "Member database insertion"; break;
            case self::MAIN_DB_INSERTION    : $message = "Main database insertion"; break;
            case self::SEND_EMAIL           : $message = "Sending signup email"; break;
            case self::MEMBER_UNLOCK        : $message = "Unlocking new member"; break;
        }

        return $message;
    }
}
?>
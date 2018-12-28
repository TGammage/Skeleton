<?php

namespace SystemCore\Login;

class LoginError
{
    /**
     * Constants
     */
    const NO_ERROR                  = 1000;
    const MISSING_IDENTIFIER        = 1001;
    const MISSING_PASSWORD          = 1002;
    const FORMAT_IDENTIFIER         = 1003;
    const FORMAT_PASSWORD           = 1004;
    const INCORRECT_PASSWORD        = 1005;
    const ACCOUNT_NOT_FOUND         = 1006;
    const ACCOUNT_LOCKED            = 1007;
    const ACCOUNT_BANNED            = 1008;
    //-----Server Visibility Only-----
    const BACKSIDE_ONLY             = 1009;
    const CONFIG_SETTING            = 1010;
    const SESSION_CREATE_DATABASE   = 1011;
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

            case self::NO_ERROR                 : $message = "Signup successful"; break;
            case self::MISSING_IDENTIFIER       : $message = "Email field not found"; break;
            case self::MISSING_PASSWORD         : $message = "Password field not found"; break;
            case self::FORMAT_IDENTIFIER        : $message = "Identifier format unaccepted"; break;
            case self::FORMAT_PASSWORD          : $message = "Password format unaccepted"; break;
            case self::INCORRECT_PASSWORD       : $message = "Incorrect Password"; break;
            case self::ACCOUNT_NOT_FOUND        : $message = "Account does not exist"; break;
            case self::ACCOUNT_BANNED           : $message = "Account banned"; break;
            case self::ACCOUNT_LOCKED           : $message = "Account locked"; break;
            //-----Server Visibility Only-----
            case self::CONFIG_SETTING           : $message = "Configuration setting invalid, check Login Identify By"; break;
            case self::SESSION_CREATE_DATABASE  : $message = "Failed to create session in database"; break;
        }

        return $message;
    }
}
?>
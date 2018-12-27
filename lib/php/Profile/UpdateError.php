<?php

namespace SystemCore\Profile;

class UpdateError
{
    /**
     * Constants
     */
    const NO_ERROR          = 1000;
    const INVALID_EMAIL     = 1001;
    const DUPLICATE_EMAIL   = 1002;
    const FORMAT_FIRST_NAME = 1003;
    const FORMAT_LAST_NAME  = 1004;
    const FORMAT_PASSWORD   = 1005;
    const CONFIRM_PASSWORD  = 1006;
    const DATABASE_UPDATE   = 1007;

    public static function message( $code )
    {
        switch( $code )
        {
            default : $message = "General Error"; break;

            case self::NO_ERROR             : $message = "Profile updated successfully"; break;
            case self::INVALID_EMAIL        : $message = "Email address unaccepted"; break;
            case self::DUPLICATE_EMAIL      : $message = "Email address exists on database"; break;
            case self::FORMAT_FIRST_NAME    : $message = "First name format unaccepted"; break;
            case self::FORMAT_LAST_NAME     : $message = "Last name format unaccepted"; break;
            case self::FORMAT_PASSWORD      : $message = "Password format unaccepted"; break;
            case self::CONFIRM_PASSWORD     : $message = "Password confirmation mismatch"; break;
            case self::DATABASE_UPDATE      : $message = "Profile update failed"; break;
        }

        return $message;
    }
}

?>
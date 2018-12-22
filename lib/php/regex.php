<?php


class regex
{
    /**
     * Constants
     */
    const USERNAME      = '/^[a-zA-Z0-9]{3,12}$/';
    const PASSWORD      = '/^(?=.*[!@#\$%\^&\*\(\)\+\-=_\'\"\<\>\?\\/\[\]\{\}:;,\.|`~’])(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
    const FIRST_NAME    = '/^(?=.*[A-Za-z])[A-Za-z0-9 \-\'’]{1,20}$/';
    const LAST_NAME     = '/^(?=.*[A-Za-z])[A-Za-z0-9 \-\'’]{1,30}$/';
}
?>
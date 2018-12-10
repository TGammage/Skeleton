:start
@echo off
set run=y

:run_tick
cls
php c:\wamp64\www\Skeleton\lib\php\tick\tick.php asd
set run=n

echo.
echo.
echo Would you like to repeat the script%? (y/n)
set /p run=

if %run%==y (
	goto :run_tick
)else (
	goto :eof
)

Set oShell = CreateObject ("Wscript.Shell") 
Dim strArgs
strArgs = "cmd /c/wamp64/www/Skeleton/lib/php/tick.cmd"
oShell.Run strArgs, 0, false
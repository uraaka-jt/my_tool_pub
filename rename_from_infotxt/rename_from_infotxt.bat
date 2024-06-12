@echo off

if "%~1"=="" (
  echo "not args."
  pause
  exit /b
)

php rename_from_infotxt.php %*

rem 上記で動かない場合は、ツールの設置場所を指定する
rem php C:\XXXXXXX\my_tool_pub\rename_from_infotxt.php %*

rem phpのコマンドが使えない場合は、php.exeが設置してる場所を指定する必要がある
rem C:\XXXX\php.exe C:\XXXXXXX\my_tool_pub\rename_from_infotxt.php %*

rem pause
rem exit /b

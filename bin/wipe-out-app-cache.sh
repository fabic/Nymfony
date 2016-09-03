#!/bin/bash
#
# 2016-09-03

Env="all"

PhpBinary="$( type -p php )"

AppConsole="app/console"

# CLI arg. :
if [ $# -gt 0 ]; then
	Env=${1:?"The Symfony app. environment (e.g. dev, prod, test)."}
fi

# Try first from the current dir. in case we have sub-projects
# that have their own Symfony app.
if [ ! -d "app/cache" ];
then
	echo "(!) Moving up out of bin/"
	cd "$( dirname "$0" )/.." || exit 127
	echo "> Now in `pwd`"
fi

# Ensure we _do_ have an app/cache/ sub-dir. :
if [ ! -d "app/cache" ];
then
	echo "(!) FAIL : couldn't find the app/cache/ sub-directory here at `pwd`"
	exit 125
fi

# Can't be -_-
if [ ! -f "$AppConsole" ];
then
	echo
	echo "(!) WARNING (!)"
	echo "(!) WARNING (!) Couldn't find the '$AppConsole' Symfony application CLI/Console script."
	echo "(!) WARNING (!)"
	echo
fi

# List env. sub-directories (for information).
if ! find app/cache/ -maxdepth 1 -type d -ls ;
then
	echo "(!!) FAIL : Couldn't list content of app/cache/"
	exit 123
fi

# Wipe out everything under app/cache/ ?
if [ "$Env" == "all" ];
then
	echo
	echo "> About to remove everything under app/cache/..."
	echo

	rm -rf "app/cache/*"

	retv=$?

	if [ $retv -gt 0 ]; then
		echo
		echo "(!!) WARNING (!!) : Ouch! the 'rm -rf app/cache/*' command exited with non-zero status code $retv ;"
		echo "                    and this is generally not an expected outcome, please check the file-system directories & files permissions (etc...)"
		echo 
		echo "                    ( CONTINUING ANYWAY FYI )"
		echo 
	fi

	# For the cache:clear/warmup later on.
	Env="prod"
fi


Cmd=( "$PhpBinary" -f "$AppConsole" -- cache:clear --env="$Env" )

echo "> About to run command :"
echo "  ${Cmd[@]}"

# RUN !
"${Cmd[@]}"

retv=$?

echo
echo "> Exit status : $retv  ( of command: ${Cmd[@]} )"

if [ $retv -gt 0 ];
then
	echo
	echo "(!) WARNING (!) : Non-zero exit status ain't no good sign buddy -_-"
	echo
fi

exit $retv

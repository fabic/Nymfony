#!/bin/sh
#
# F.2013-04-01
#
# A simple shortcut for grep-ing...
#
# TODO: Exclude app/cache/, app/logs/, tmp/, ...
#    Fixme: The exclude pattern for app/cache/ & app/logs/ should be more specific!
#
# TODO: pgrep, egrep ?

# Real path to us so that we can be symlinked, hummm, why?
HERE=$(cd `dirname $(realpath "$0")` && pwd )

# Usage
if [ $# -lt 1 ]; then
    echo "This is actually a GREP wrapper for searching your PHP/Symfony2 code."
    echo "Usage: $0 <any grep argument>"
    echo "       $0 <some_grep_arguments> <pattern> [dir1/ dir2/ ... dirN/]"
    echo "Examples:"
    echo "    $0 DefaultController src/ vendor/acme/
    echo "    $0 -i framework app/"
    exit 1
fi

cd "$HERE"

echo "INFO: Grepping from \``pwd`'"

grep -rHn \
	--exclude-dir='.*' \
	--exclude-dir=cache \
	--exclude-dir=logs \
	--exclude-dir=Tests \
	--binary-files=without-match \
	--include='*.php' --include='*.phtml' --include='*.yml' --include='*.xml' \
	"$@"

# vim:ft=sh

#!/bin/sh
#
# F.2013-04-01
#
# A simple shortcut for grep-ing...
#

# Real path to us so that we can be symlinked.
HERE=$(cd `dirname $(realpath "$0")` && pwd )

# Usage
if [ $# -lt 1 ]; then
	echo
	echo "Usage: $0 <grep arguments>"
	echo "  Grep for stuff from your project root ($HERE)"
	echo
	exit 1
fi

cd "$HERE"

echo "INFO: Grepping from \``pwd`'"

grep -rHni \
	--exclude-dir='.*' \
	--exclude-dir=cache \
	--exclude-dir=logs \
	--exclude-dir=Tests \
	--binary-files=without-match \
	--include='*.php' --include='*.phtml' --include='*.yml' --include='*.xml' \
	"$@"

# vim:ft=sh

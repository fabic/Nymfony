#!/bin/sh
HERE=$(cd `dirname "$0"` && pwd)

cd "$HERE"

rm app/cache/* app/logs/* -rf

find app/cache/ app/logs/

#!/bin/sh
# wp-content/plugins/jeffpack-multi-git/git-move-host.sh

# DIR is the full path of wp-content/plugins/jeffpack-multi-git/
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# WP_ROOT is the parent dir of wp-content
WP_ROOT="$( cd $DIR && cd ../../../ && pwd )"

cd "$WP_ROOT"
git --work-tree="$WP_ROOT" --git-dir="$DIR/dotgit-move-host" "$@"
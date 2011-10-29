#!/bin/bash

# args for trunk
MSG=${1-''}
BRANCH=${2-'trunk'}

# args for tagging a version x.y
# MSG=${1-'tagging x.y'}
# BRANCH=${2-'tags/x.y'}

# paths
SRC_DIR=$(pwd)
DIR_NAME=$(basename $SRC_DIR)
DEST_DIR=/Volumes/MacData/Projects/Wordpress/plugins/svn/$DIR_NAME/$BRANCH

# make sure the destination dir exists
svn mkdir $DEST_DIR 2> /dev/null
svn add $DEST_DIR 2> /dev/null

# delete everything except .svn dirs
for file in $(find $DEST_DIR/* -not -path "*.svn*")
do
	rm $file 2>/dev/null
done

# copy everything over from git
rsync -r --exclude='*.git*' $SRC_DIR/* $DEST_DIR

cd $DEST_DIR

# check .svnignore
for file in $(cat "$SRC_DIR/.svnignore" 2>/dev/null)
do
	rm -rf $file
done

# svn addremove
svn stat | grep '^\?' | awk '{print $2}' | xargs svn add > /dev/null 2>&1
svn stat | grep '^\!' | awk '{print $2}' | xargs svn rm  > /dev/null 2>&1

svn stat

svn ci -m "$MSG"


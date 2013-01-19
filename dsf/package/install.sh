#!/bin/sh

UGMIRROR="UG@is-great.org"

rm -rf "/persistfs/main_screen_items/$UGMIRROR"
cp -rp "$UGMIRROR" /persistfs/main_screen_items

#!/bin/bash
set -e
git pull
./yii cache/flush-all
./yii migrate/up
composer install
./minify.sh
echo "--------------------------------------------------------------"
echo "-                                                            -"
echo "-                                                            -"
echo "-                          SUCCESS                           -"
echo "-                                                            -"
echo "-                                                            -"
echo "--------------------------------------------------------------"
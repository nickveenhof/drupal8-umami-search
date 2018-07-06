#!/bin/sh
SCRIPT_DIR="$(cd -- "$(dirname "$0")"; pwd -P)"

DRUSH="$SCRIPT_DIR/../../vendor/bin/drush"
if [ -f ${DRUSH} ]; then
  VERSION=$(${DRUSH} --version)
  echo "Using project-specific drush library"
else
  DRUSH=""
  echo "No project specific drush found, checking global version"
  if [ -e $(command -v drush8) ]; then
    DRUSH="drush8"
  elif [ -e $(command -v drush) ]; then
    DRUSH="drush"
  fi
fi

if [ -e ${DRUSH} ]; then
  VERSION=$(${DRUSH} --version --pipe)
  echo "Drush version: ${VERSION}"
else
    echo "Drush not found! exit"
  exit 1
fi

DOCROOT="$SCRIPT_DIR/../../docroot"
cd $DOCROOT

# D8

# Update site
${DRUSH} updb -y
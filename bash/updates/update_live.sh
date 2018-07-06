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
rm ../.ht.sqlite
composer install
mkdir docroot/themes/contrib
mkdir docroot/libraries/jquery-ui-slider-pips
wget -P docroot/libraries/jquery-ui-slider-pips https://raw.githubusercontent.com/simeydotme/jQuery-ui-Slider-Pips/v1.11.3/dist/jquery-ui-slider-pips.min.css
wget -P docroot/libraries/jquery-ui-slider-pips https://raw.githubusercontent.com/simeydotme/jQuery-ui-Slider-Pips/v1.11.3/dist/jquery-ui-slider-pips.min.js
cp -R docroot/core/profiles/demo_umami/themes/umami docroot/themes/contrib/
cp -R docroot/core/profiles/demo_umami/modules/demo_umami_content docroot/modules/contrib/
${DRUSH} site-install --db-url=sqlite://../.ht.sqlite --verbose config_installer config_installer_sync_configure_form.sync_directory=../config/sync/ --yes
${DRUSH} ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_content\InstallHelper::class)->importContent();'
${DRUSH} search-api:reset-tracker
${DRUSH} search-api:index
${DRUSH} cr

#!/usr/bin/env bash

composer install
mkdir docroot/themes/contrib
rm -rf docroot/libraries/jquery-ui-slider-pips
mkdir docroot/libraries/jquery-ui-slider-pips
wget -P docroot/libraries/jquery-ui-slider-pips https://raw.githubusercontent.com/simeydotme/jQuery-ui-Slider-Pips/v1.11.3/dist/jquery-ui-slider-pips.min.css
wget -P docroot/libraries/jquery-ui-slider-pips https://raw.githubusercontent.com/simeydotme/jQuery-ui-Slider-Pips/v1.11.3/dist/jquery-ui-slider-pips.min.js
cp -R docroot/core/profiles/demo_umami/themes/umami docroot/themes/contrib/
cp -R docroot/core/profiles/demo_umami/modules/demo_umami_content docroot/modules/contrib/
rm .ht.sqlite || :
sudo rm -rf web/sites/default/settings.php
drush site-install --db-url=sqlite://.ht.sqlite config_installer config_installer_sync_configure_form.sync_directory=../config/sync/ --yes
drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_content\InstallHelper::class)->importContent();'
drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_search_content\InstallHelper::class)->importContent();'
drush search-api:reset-tracker
drush search-api:index
drush cr

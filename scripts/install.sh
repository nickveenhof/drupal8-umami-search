#!/usr/bin/env bash

composer install
mkdir docroot/themes/contrib
cp -R docroot/core/profiles/demo_umami/themes/umami docroot/themes/contrib/
cp -R docroot/core/profiles/demo_umami/modules/demo_umami_content docroot/modules/contrib/
rm .ht.sqlite || :
sudo rm -rf web/sites/default/settings.php
vendor/bin/drush site-install --db-url=sqlite://.ht.sqlite config_installer config_installer_sync_configure_form.sync_directory=../config/sync/ --yes
vendor/bin/drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_content\InstallHelper::class)->importContent();'
vendor/bin/drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_search_content\InstallHelper::class)->importContent();'
vendor/bin/drush search-api:reset-tracker
vendor/bin/drush search-api:index
vendor/bin/drush cr

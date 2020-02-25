#!/bin/sh

SCRIPT_DIR="$(cd -- "$(dirname "$0")"; pwd -P)"
DOCROOT="$SCRIPT_DIR/../../docroot"
cd $SCRIPT_DIR/../../

composer install
sleep 1
cp -R docroot/core/profiles/demo_umami/themes/umami docroot/themes/contrib/
sleep 1
cp -R docroot/core/profiles/demo_umami/modules/demo_umami_content docroot/modules/contrib/
sleep 1
rm .ht.sqlite || :
sleep 1
chmod +w docroot/sites/default
sleep 1
rm docroot/sites/default/settings.php || :
sleep 1
vendor/bin/drush site-install --account-name=drupalsearch --account-pass=drupalsearch --db-url=sqlite://../.ht.sqlite config_installer config_installer_sync_configure_form.sync_directory=../config/sync/ --yes
sleep 1
vendor/bin/drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_content\InstallHelper::class)->importContent();'
sleep 1
vendor/bin/drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_search_content\InstallHelper::class)->importContent();'
sleep 1
vendor/bin/drush search-api:reset-tracker
sleep 1
vendor/bin/drush search-api:index
sleep 1
vendor/bin/drush cr
sleep 1
vendor/bin/drush en config_readonly
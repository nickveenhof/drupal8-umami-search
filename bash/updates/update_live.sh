#!/bin/sh

SCRIPT_DIR="$(cd -- "$(dirname "$0")"; pwd -P)"
DOCROOT="$SCRIPT_DIR/../../docroot"
cd $SCRIPT_DIR/../../

composer install
cp -R docroot/core/profiles/demo_umami/themes/umami docroot/themes/contrib/
cp -R docroot/core/profiles/demo_umami/modules/demo_umami_content docroot/modules/contrib/
rm .ht.sqlite || :
rm -rf docroot/sites/default/settings.php || :
chmod +w docroot/sites/default
vendor/bin/drush site-install --account-name=drupalsearch --account-pass=drupalsearch --db-url=sqlite://.ht.sqlite config_installer config_installer_sync_configure_form.sync_directory=../config/sync/ --yes
vendor/bin/drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_content\InstallHelper::class)->importContent();'
vendor/bin/drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_search_content\InstallHelper::class)->importContent();'
vendor/bin/drush search-api:reset-tracker
vendor/bin/drush search-api:index
vendor/bin/drush cr
vendor/bin/drush en config_readonly
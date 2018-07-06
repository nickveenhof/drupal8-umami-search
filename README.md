# Umami demo search

This project is an implementation of umami to be used as a demo.

This code is hosted on [drupalsear.ch](http://drupalsear.ch). This provides an example of what can be achieved with a basic setup of Search API, Search API db, and Facets.

The site can be installed locally as follows:

* go to the root of the directory
* composer install
* mkdir docroot/themes/contrib
* cp -R docroot/core/profiles/demo_umami/themes/umami docroot/themes/contrib/
* cp -R docroot/core/profiles/demo_umami/modules/demo_umami_content docroot/modules/contrib/
* drush site-install --verbose config_installer config_installer_sync_configure_form.sync_directory=config --yes
* drush ev '\Drupal::classResolver()->getInstanceFromDefinition(Drupal\demo_umami_content\InstallHelper::class)->importContent();'
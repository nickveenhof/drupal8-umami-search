<?php
      $update_free_access = FALSE;


      $settings['hash_salt'] = '087ded9ae62a2b0c83eac4bff1b3ec47';
      $config_directories['sync'] = DRUPAL_ROOT . '/../config/sync';

      ini_set('session.gc_probability', 1);
      ini_set('session.gc_divisor', 100);
      ini_set('session.gc_maxlifetime', 200000);
      ini_set('session.cookie_lifetime', 2000000);
       
      /**
       * Load services definition file.
       */
      $settings['container_yamls'][] = __DIR__ . '/services.yml';

      /**
       *  Show install profile in reports page
       */
      $settings['install_profile'] = 'demo_umami';

      /**
       * Make sure our config cannot be changed from the UI.
       */
      $settings['config_readonly'] = TRUE;
      $settings['install_profile'] = 'config_installer';

      if (file_exists(DRUPAL_ROOT . '/../etc/drupal/additional_settings.dev.php')) {
        include DRUPAL_ROOT . '/../etc/drupal/additional_settings.dev.php';
      }
$config["system.file"]["path"]["temporary"] = "../tmp";


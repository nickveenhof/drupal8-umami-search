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

      /**
       * Varnish
       */
      // Tell Drupal that we are behind a reverse proxy server
      $conf['reverse_proxy'] = TRUE;

      // List of trusted IPs (IP numbers of our reverse proxies)
      $conf['reverse_proxy_addresses'] = array(
        '127.0.0.1',
      );
      $conf['varnish_flush_cron'] = 0;
      $conf['varnish_version'] = '3';
      $conf['varnish_control_terminal'] = '127.0.0.1:6082';
      $conf['varnish_control_key'] = 'WbEHEnos-9ZV6-c6v7-925w-WAF534581Uhi';
      $conf['varnish_socket_timeout'] = '250';
      $conf['varnish_cache_clear'] = 1;//Drupal default will clear all page caches on node updates and cache flush events. None will allow pages to persist for their full max-age
      $conf['varnish_bantype'] = 0;
      $conf['cache_backends'][] = 'sites/all/modules/contrib/varnish/varnish.cache.inc';
      $conf['cache_class_cache_page'] = 'VarnishCache';
      // Drupal 7 does not cache pages when we invoke hooks during bootstrap. This needs
      // to be disabled.
      $conf['page_cache_invoke_hooks'] = FALSE;

      if (file_exists(DRUPAL_ROOT . '/../etc/drupal/additional_settings.live.php')) {
        include DRUPAL_ROOT . '/../etc/drupal/additional_settings.live.php';
      }

      $config["system.file"]["path"]["temporary"] = "../tmp";

      // BEGIN reverse proxy setting added by Dropsolid infrastructure, DO NOT EDIT
      // Tell Drupal that we are behind a reverse proxy server
      $settings['reverse_proxy'] = TRUE;

      // List of trusted IPs (IP numbers of our reverse proxies)
      $settings['reverse_proxy_addresses'] = ['127.0.0.1', '35.187.168.7', '104.199.104.19', '35.195.110.3'];
      // END reverse proxy setting added by Dropsolid infrastructure, DO NOT EDIT

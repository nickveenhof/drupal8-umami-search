<?php
// Project name
$project      = 'drupalsearchdemosite';

/**
 * This function gathers and validates a list of public-facing IPv4 addresses
 * on the machine this script is running on. It will attempt to use the Google
 * Cloud API first. If this fails, it will attempt to parse the output of the
 * ip address or ifconfig commands.
 *
 * @return array
 *   Valid public IP addresses of localhost, if found.
 */
if (!function_exists('dropsolidGetIP')) {
    function dropsolidGetIP()
    {
        $ipList = array();
        $gcIp   = dropsolidCallAPI("http://metadata.google.internal/computeMetadata/v1/instance/network-interfaces/0/access-configs/0/external-ip", array("Metadata-Flavor: Google"));
        // If Google API worked
        if (filter_var($gcIp, FILTER_VALIDATE_IP) !== false) {
            $ipList[] = $gcIp;
            return $ipList;
        }

        $parsedIp = array();
        // If iproute2util is installed
        $hasIpBinary = shell_exec("command -v ip");
        if (!empty($hasIpBinary)) {
            $output   = shell_exec("ip address | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $2}' | cut -d'/' -f1");
            $parsedIp = explode(PHP_EOL, $output);
        }
        // Else assume ifconfig is available
        else {
            $output   = shell_exec("ifconfig | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $2}' | cut -d':' -f2");
            $parsedIp = explode(PHP_EOL, $output);
        }
        // Validate each IP to be a real IP
        foreach ($parsedIp as $addr) {
            if (filter_var($addr, FILTER_VALIDATE_IP) !== false) {
                $ipList[] = $addr;
            }
        }
        return $ipList;
    }
}

/**
 * This function calls an API using a cURL HTTP GET.
 *
 * @param string $url
 *   The URL to which the API call should be made.
 *
 * @param string $header
 *   The HTTP headers to pass along with the API call.
 *
 * @return string
 *   The output of the API call.
 */
if (!function_exists('dropsolidCallApi')) {
    function dropsolidCallApi($url, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

/**
 * This function checks if there are other alias files to consider and detect the
 * folder that they are in. This is a helper function to aid with deciding what
 * other drush alias files to load in.
 *
 * @param string $project
 *   The project name to resolve the drush alias filename with.
 *
 * @return array
 *   An array of local alias filenames with their full paths.
 */
if (!function_exists('dropsolidGetLocalAliases')) {
  function dropsolidGetLocalAliases($project) {
    // We are getting the paths here because requiring them from within the function
    // does not work.
    $local_drush_files = array();

    // _drush_sitealias_find_alias_files() is a built-in drush function that builds an
    // array of all known alias files. See:
    // http://api.drush.org/api/drush/includes%21sitealias.inc/function/_drush_sitealias_find_alias_files/8.0.x
    $alias_files_to_consider = _drush_sitealias_find_alias_files();
    if (!empty($alias_files_to_consider) && is_array($alias_files_to_consider)) {
      $drush_alias_folders = array();
      // List all the distinct folders.
      foreach ($alias_files_to_consider as $alias_file) {
        $drush_dir = dirname($alias_file);
        if (!in_array($drush_dir, $drush_alias_folders)) {
          $drush_alias_folders[] = $drush_dir;
        }
      }
      // Loop over all folders that we found and try to find project specific local includes.
      foreach ($drush_alias_folders as $drush_alias_folder) {
        if (file_exists(sprintf('%s/%s.local.php', $drush_alias_folder, $project))) {
          $local_drush_files[] = sprintf('%s/%s.local.php', $drush_alias_folder, $project);
        }
      }
    }
    return $local_drush_files;
  }
}

// Generate a list of public-facing IP addresses of the local machine.
$addrList = dropsolidGetIP();

// live
// Server
$env_live     = 'shared05.dropsolid.com';
// User
$remote_user_live     = drupalsearchdemosite;

// Alias
$aliases[$project . '.live'] = array(
    'root'                    => '/var/www/drupalsearchdemosite/docroot',
    'path-aliases'            => array(
        '%dump' => sprintf('/tmp/sql-sync-live-%s-local.sql', $project),
        '%drush' => '/usr/bin/drush8',
        '%drush-script' => '/usr/bin/drush8',
    ),
    'ssh-options' => '-o StrictHostKeyChecking=no',
    'command-specific'        => array(
        'core-rsync' => array(
            'mode' => 'rlvz',
            'chmod' => 'ugo=rwX',
            'perms' => TRUE
        ),
        'rsync'      => array(
            'mode' => 'rlvz',
            'chmod' => 'ugo=rwX',
            'perms' => TRUE
        ),
    ),
    'target-command-specific' => array(
        'sql-sync' => array(
            'no-ordered-dump' => true,
        ),
    ),
);

// staging
// Server
$env_staging     = 'eos.dropsolid.com';
// User
$remote_user_staging     = $project;

// Alias
$aliases[$project . '.staging'] = array(
    'root'                    => '/var/www/customprojects_D8/drupalsearchdemosite/staging/docroot',
    'path-aliases'            => array(
        '%dump' => sprintf('/tmp/sql-sync-staging-%s-local.sql', $project),
        '%drush' => '/usr/bin/drush8',
        '%drush-script' => '/usr/bin/drush8',
    ),
    'ssh-options' => '-o StrictHostKeyChecking=no',
    'command-specific'        => array(
        'core-rsync' => array(
            'mode' => 'rlvz',
            'chmod' => 'ugo=rwX',
            'perms' => TRUE
        ),
        'rsync'      => array(
            'mode' => 'rlvz',
            'chmod' => 'ugo=rwX',
            'perms' => TRUE
        ),
    ),
    'target-command-specific' => array(
        'sql-sync' => array(
            'no-ordered-dump' => true,
        ),
    ),
);

// dev
// Server
$env_dev     = 'eos.dropsolid.com';
// User
$remote_user_dev     = $project;

// Alias
$aliases[$project . '.dev'] = array(
    'root'                    => '/var/www/customprojects_D8/drupalsearchdemosite/dev/docroot',
    'path-aliases'            => array(
        '%dump' => sprintf('/tmp/sql-sync-dev-%s-local.sql', $project),
        '%drush' => '/usr/bin/drush8',
        '%drush-script' => '/usr/bin/drush8',
    ),
    'ssh-options' => '-o StrictHostKeyChecking=no',
    'command-specific'        => array(
        'core-rsync' => array(
            'mode' => 'rlvz',
            'chmod' => 'ugo=rwX',
            'perms' => TRUE
        ),
        'rsync'      => array(
            'mode' => 'rlvz',
            'chmod' => 'ugo=rwX',
            'perms' => TRUE
        ),
    ),
    'target-command-specific' => array(
        'sql-sync' => array(
            'no-ordered-dump' => true,
        ),
    ),
);

// Get the paths to all local alias files.
$local_files = dropsolidGetLocalAliases($project);

// Load in local drush alias files. There are no local alias files on the servers but
// this line allows developers to append and overwrite this file for their local
// environment.
if (!empty($local_files)) {
  foreach ($local_files as $local_file) {
    // We don't need to check if the file exists because the get function 
    // already does that for us. We require the alias file within the scope
    // of this file. Has to be include instead of include_once because drush
    // overwrites this if an alias is loaded in more than once, such as when
    // drush sql-sync is used.
    include $local_file;
  }
}

// When the project is not hosted on the same server where this script is
// running on, add the remote host parameters for the respective environment.
// This is located after the include to allow developers to overwrite 
// remote-host and remote-user with their own values.
if (!in_array($env_live, $addrList) && !in_array(gethostbyname($env_live), $addrList)) {
    $aliases[$project . '.live']['remote-host'] = $env_live;
    $aliases[$project . '.live']['remote-user'] = $remote_user_live;
}
if (!in_array($env_staging, $addrList) && !in_array(gethostbyname($env_staging), $addrList)) {
    $aliases[$project . '.staging']['remote-host'] = $env_staging;
    $aliases[$project . '.staging']['remote-user'] = $remote_user_staging;
}
if (!in_array($env_dev, $addrList) && !in_array(gethostbyname($env_dev), $addrList)) {
    $aliases[$project . '.dev']['remote-host'] = $env_dev;
    $aliases[$project . '.dev']['remote-user'] = $remote_user_dev;
}

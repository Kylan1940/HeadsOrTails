<?php

namespace Kylan1940\HeadsOrTails\UpdateNotifier;

use Kylan1940\HeadsOrTails\Main;

class ConfigUpdater {
  
  const CONFIG_VERSION = 1;
  
  public static function update(Main $plugin){
    if (!file_exists($plugin->getDataFolder() . 'config.yml')) {
      $plugin->saveResource('config.yml');
      return;
    }
    if ($plugin->getConfig()->get('config-version') !== self::CONFIG_VERSION) {
      $config_version = $plugin->getConfig()->get('config-version');
      $plugin->getLogger()->info("Your Config isn't the latest. We renamed your old config to §bconfig-" . $config_version . ".yml §6and created a new config");
      rename($plugin->getDataFolder() . 'config.yml', $plugin->getDataFolder() . 'config-' . $config_version . '.yml');
      $plugin->saveResource('config.yml');
    }
  }
  
}
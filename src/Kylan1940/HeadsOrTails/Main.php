<?php

namespace Kylan1940\HeadsOrTails;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};

class Main extends PluginBase implements Listener {
  
  const CONFIG_VERSION = 1;
  const PREFIX = "prefix";
  
  public function onEnable() : void {
        $this->updateConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getResource("config.yml");
  }
  
  private function updateConfig(){
        if (!file_exists($this->getDataFolder() . 'config.yml')) {
            $this->saveResource('config.yml');
            return;
        }
        if ($this->getConfig()->get('config-version') !== self::CONFIG_VERSION) {
            $config_version = $this->getConfig()->get('config-version');
            $this->getLogger()->info("Your Config isn't the latest. We renamed your old config to §bconfig-" . $config_version . ".yml §6and created a new config");
            rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config-' . $config_version . '.yml');
            $this->saveResource('config.yml');
        }
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
    $randomNumber = random_int(0, 1);
    $result = ($randomNumber === 0) ? "heads" : "tails";
    $prefix = $this->getConfig()->get(self::PREFIX);
    $announcementInvalid = ["broadcast", "message"];
        if($sender instanceof Player){
                if($cmd->getName() == "heads"){
                  if($sender->hasPermission("headsortails.command")){
                    if($result == $cmd->getName()){
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.win')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.win')));
                      } 
                    } else {
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.lose')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.lose')));
                      } 
                    }
                  } else {
                    $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.no-permission.play')));
                  }
                }
                if($cmd->getName() == "tails"){
                  if($sender->hasPermission("headsortails.command")){
                    if($result == $cmd->getName()){
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.win')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.win')));
                      } 
                    } else {
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.lose')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.lose')));
                      }
                    }
                  } else {
                    $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.no-permission.play')));
                  }
                }
                if($cmd->getName() == "headsortails"){
                  if ($sender -> hasPermission("headsortails.ui")) {
                    $this->HOT($sender);
                    } else {
                      $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.no-permission.ui')));
                    } 
                  }
        } 
        if(!$sender instanceof Player){
                if($cmd->getName() == "heads"){
                    if($result == $cmd->getName()){
                      $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.console.win')));
                    } else {
                      $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.console.lose')));
                    }
                }
                if($cmd->getName() == "tails"){
                    if($result == $cmd->getName()){
                      $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.console.win')));
                    } else {
                      $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix.$this->getConfig()->getNested('message.announce.console.lose')));
                    }
                }
                if($cmd->getName() == "headsortails"){
                  $sender->sendMessage($prefix.$this->getConfig()->getNested('message.console.no-ui-support'));
                }
        } 
    return true;
  }
   
  public function HOT($sender){
        $form = new SimpleForm(function (Player $sender, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                  $formName = "heads";
                  $randomNumber = random_int(0, 1);
                  $result = ($randomNumber === 0) ? "heads" : "tails";
                  $prefix = $this->getConfig()->get(self::PREFIX); 
                  if($result == $formName){
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.win')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.win')));
                      } 
                  } else {
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.lose')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.lose')));
                      }
                  }
                  break;
                case 1:
                  $formName = "tails";
                  $randomNumber = random_int(0, 1);
                  $result = ($randomNumber === 0) ? "heads" : "tails";
                  $prefix = $this->getConfig()->get(self::PREFIX); 
                  if($result == $formName){
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.win')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.win')));
                      } 
                  } else {
                      if($this->getConfig()->getNested('announcement') == "broadcast"){
                        $sender->getServer()->broadcastMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.broadcast.lose')));
                      } 
                      elseif ($this->getConfig()->getNested('announcement') == "message"){
                        $sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $formName, $result], $prefix.$this->getConfig()->getNested('message.announce.ingame.message.lose')));
                      }
                  }
                  break;
            }
        });
            $form->setTitle($this->getConfig()->getNested('ui.title'));
            $form->addButton($this->getConfig()->getNested('ui.button.heads'));
            $form->addButton($this->getConfig()->getNested('ui.button.tails'));
            $form->sendToPlayer($sender);
            return $form;
    }
}

<?php

namespace Kylan1940\HeadsOrTails;

use Kylan1940\HeadsOrTails\Form\SimpleForm;
use Kylan1940\HeadsOrTails\UpdateNotifier\ConfigUpdater;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use function file_exists;
use function random_int;
use function rename;
use function str_replace;

class Main extends PluginBase implements Listener {
  
  const PREFIX = "prefix";

  public function onEnable(): void {
		ConfigUpdater::update($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		$result = (random_int(0, 1) === 0) ? "heads" : "tails";
		$prefix = $this->getConfig()->get(self::PREFIX);

		if ($cmd->getName() == "heads" || $cmd->getName() == "tails") {
			if (!$sender->hasPermission("headsortails.command")) {
				$sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix . $this->getConfig()->getNested('message.no-permission.play')));
				return false;
			}

			$this->sendResult($sender, $cmd->getName(), $result);
			return true;
		} else if ($cmd->getName() == "headsortails") {
			if ($sender instanceof Player) {
				if ($sender->hasPermission("headsortails.ui")) {
					$this->HOT($sender);
					return true;
				}

				$sender->sendMessage(str_replace(["{name}", "{choose}", "{result}"], [$sender->getName(), $cmd->getName(), $result], $prefix . $this->getConfig()->getNested('message.no-permission.ui')));
			} else {
				$sender->sendMessage($prefix . $this->getConfig()->getNested('message.console.no-ui-support'));
			}
		}

		return false;
  }

  public function HOT(Player $sender): void {
		$form = new SimpleForm(function (Player $sender, int $result = null){
			if ($result === 0) {
				$this->sendResult($sender, "heads", (random_int(0, 1) === 0 ? "heads" : "tails"));
			} elseif ($result === 1) {
				$this->sendResult($sender, "tails", (random_int(0, 1) === 0 ? "heads" : "tails"));
			}
		});
		$form->setTitle($this->getConfig()->getNested('ui.title'));
		$form->addButton($this->getConfig()->getNested('ui.button.heads'));
		$form->addButton($this->getConfig()->getNested('ui.button.tails'));
		$form->sendToPlayer($sender);
	}

	private function generateMessage(CommandSender $sender, string $type, string $choose, string $result): string {
		return str_replace(
			["{name}", "{choose}", "{result}"],
			[$sender->getName(), $choose, $result],
			$this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('message.announce.' . ($sender instanceof Player ? 'ingame' . '.' . $type : 'console')  . '.' . ($choose == $result ? "win" : "lose"))
		);
	}

	private function sendResult(CommandSender $sender, string $choose, string $result): void {
		switch ($this->getConfig()->getNested('announcement')) {
			case "broadcast":
				$sender->getServer()->broadcastMessage($this->generateMessage($sender, "broadcast", $choose, $result));
				break;
			case "message":
			default:
				$sender->sendMessage($this->generateMessage($sender, "message", $choose, $result));
				break;
		}
	}
}
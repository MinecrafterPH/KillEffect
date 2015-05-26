<?php


namespace moneyforkill;


use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\math\Vector3;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

	class moneyforkill extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->api = EconomyAPI::getInstance();
		}
	
		public function onPlayerDeathEvent(PlayerDeathEvent $event)
		{
			$player = $event->getEntity();
			$name = strtolower($player->getName());
		
			if ($player instanceof Player)
			{
				$cause = $player->getLastDamageCause();
		
				if($cause instanceof EntityDamageByEntityEvent)
				{
					$damager = $cause->getDamager();
					
					if($damager instanceof Player)
					{
						$damager->sendMessage("You killed $player.\nYou earn $50 for getting a kill.");
						$player->sendMessage("You were killed by $damager.\nYou lose $20 for getting killed.");
						$this->api->addMoney($damager, 50);
						$this->api->reduceMoney($player, 20);
					}
				}
			}
		}

		
		public function onDisable()
		{
			$this->getLogger()->info("Plugin unloaded!");
		}
	}

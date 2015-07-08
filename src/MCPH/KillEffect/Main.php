<?php

namespace MCPH\KillEffect;

use pocketmine\utils\TextFormat;
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

	class Main extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
		        $this->saveDefaultConfig();
		        $this->reloadConfig();	
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info("KillEffect has been enabled.");
			$this->api = EconomyAPI::getInstance();
			if (!$this->api) {
			$this->getLogger()->info(TextFormat::RED."Unable to find EconomyAPI.");
			return true;
			}
		}
	
		public function onPlayerDeathEvent(PlayerDeathEvent $event)
		{
			$cfg = $this->getConfig();
			$duration = $cfg->get("Duration");
			$particles = $cfg->get("Particles");
			$amplifier = $cfg->get("Amplifier");
			
			$give = $cfg->get("Add-Money");
			$take = $cfg->get("Reduce-Money");
			
			$effect = Effect::getEffect(21);
	                $effect->setVisible($particles);
	                $effect->setAmplifier($amplifier);
	                $effect->setDuration($duration);
			
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
						$damager->sendMessage("You killed ".$player.".\nYou earn $".$give." for getting a kill and a health boost!");
						$damager->addEffect($effect);
						$this->api->giveMoney($damager, $give);
						
						$player->sendMessage("You were killed by ".$damager.".\nYou lose $".$take." for getting killed.");
						$this->api->takeMoney($player, $take);
					}
				}
			}
		}

		
		public function onDisable()
		{
			$this->getLogger()->info("KillEffect has been disabled!");
		}
	}

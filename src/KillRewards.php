<?php


namespace KillRewards;


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

	class KillRewards extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
		        $this->saveDefaultConfig();
		        $this->reloadConfig();	
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info("KillRewards has been enabled.");
			$this->api = EconomyAPI::getInstance();
		}
	
		public function onPlayerDeathEvent(PlayerDeathEvent $event)
		{
			$cfg = $this->getConfig();
			$effectid = $cfg->get("Effect-ID");
			$duration = $cfg->get("Duration");
			$particles = $cfg->get("Particles");
			$amplifier = $cfg->get("Amplifier");
			
			$add = $cfg->get("Add-Money")
			$reduce = $cfg->get("Remove-Money")
			
			$effect = Effect::getEffect($effectid); //Effect ID
	                $effect->setVisible($particles); //Particles
	                $effect->setAmplifier($amplifier);
	                $effect->setDuration($duration); //Ticks
			
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
						$damager->sendMessage("You killed $player.\nYou earn $$add for getting a kill and a health boost for $duration seconds.");
						$damager->addEffect($effect);
						$this->api->addMoney($damager, $add);
						
						$player->sendMessage("You were killed by $damager.\nYou lose $$reduce for getting killed.");
						$this->api->reduceMoney($player, $reduce);
					}
				}
			}
		}

		
		public function onDisable()
		{
			$this->getLogger()->info("Plugin unloaded!");
		}
	}

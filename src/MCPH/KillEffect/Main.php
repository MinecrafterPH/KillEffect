<?php

namespace MCPH\KillEffect;

use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use onebone\economyapi\EconomyAPI;


	class Main extends PluginBase implements Listener
	
	{
		
		public function onEnable()
		{
		        $this->saveDefaultConfig();
		        $this->reloadConfig();	
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(TextFormat::YELLOW . "[" . TextFormat::GOLD . "KillEffect" . TextFormat::YELLOW. "] " . TextFormat::GREEN . "Plugin has been enabled!");
			$this->getLogger()->info(TextFormat::YELLOW . "[" . TextFormat::GOLD . "KillEffect" . TextFormat::YELLOW. "] " . TextFormat::GREEN . "Created by " . TextFormat::WHITE . "MinecrafterPH");
			$this->money = EconomyAPI::getInstance();
			if (!$this->money) {
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
			$lose = $cfg->get("Lost-Money");
			
			$id = $cfg->get("Effect-ID");
			
			$effect = Effect::getEffect($id);
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
						$damager->sendMessage("You killed ".$player.".\nYou earn $".$give." for getting a kill and an effect!");
						$damager->addEffect($effect);
						$this->money->addMoney($damager, $give);
						
						$player->sendMessage("You were killed by ".$damager);
						$this->money->reduceMoney($player, $lose);
					}
				}
			}
		}

		
		public function onDisable()
		{
			$this->getLogger()->info("KillEffect has been disabled!");
		}
	}

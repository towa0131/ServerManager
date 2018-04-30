<?php

namespace ServerManager;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use ServerManager\utils\Address;

class Main extends PluginBase{

	private $config;
	private $webserver;

	private static $instance = null;

	public function onLoad(){
		self::$instance = $this;
	}

	public function onEnable(){
		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder(), 0777);
			$this->saveDefaultConfig();
		}
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$this->webserver = new WebServer(new Address($this->config->get("bind-ip"), $this->config->get("bind-port")), $this->config);
		$this->webserver->start();
	}

	public function getWebServer(): WebServer{
		return $this->webserver;
	}

	public function getConfig(): Config{
		return $this->config;
	}

	public static function getInstance(): Main{
		return self::$instance;
	}
}
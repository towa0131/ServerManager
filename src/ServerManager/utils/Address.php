<?php

namespace ServerManager\utils;

class Address{

	private $ip;
	private $port;

	public function __construct(string $ip, int $port){
		$this->isValiedAddress($ip, $port);
		$this->ip = $ip;
		$this->port = $port;
	}

	public function getIp(): string{
		return $this->ip;
	}

	public function setIp(string $ip){
		$this->ip = $ip;
	}

	public function getPort(): int{
		return $this->port;
	}

	public function setPort(int $port){
		$this->port = $port;
	}

	private function isValiedAddress(string $ip, int $port){
		if(ip2long($ip) === false){
			throw new InvaliedAddressException("Invalied IP Address");
		}

		if($port < 0 || $port > 65535){
			throw new InvaliedAddressException("Invalied Port Range");
		}
	}
}
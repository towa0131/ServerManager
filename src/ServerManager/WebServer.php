<?php


namespace ServerManager;

use pocketmine\Thread;

use pocketmine\utils\Config;

use ServerManager\utils\Address;

class WebServer extends Thread{

	protected $socket;
	protected $config;
	protected $isRunning;
	protected $address;

	public function __construct(Address $address, Config $config){
		$this->address = $address;
		$this->config = $config;
		$this->isRunning = true;
		$this->socket = @stream_socket_server("tcp://" . $address->getIp() . ":" . $address->getPort(), $errno, $errstr);
		if(!$this->socket || $errno){
			throw new WebServerException("Failed to bind to " . $this->address->getIp());
		}
		stream_set_blocking($this->socket, 0);
	}

	public function run(): void{
		while($this->isRunning){
			$socket = @stream_socket_accept($this->socket);
			if(is_resource($socket)){
				$buffer = @fread($socket, 1024);
				$endReq = strpos($buffer, "\n");
				$requestLine = substr($buffer, 0, $endReq);
				$reqArr = explode(" ", $requestLine, 3);
				$method = $reqArr[0];
				$requestUri = $reqArr[1];
				$httpVersion = $reqArr[2];
				$parsedUri = parse_url($requestUri);
				$uri = urldecode($parsedUri["path"]);
				switch($method){
					case "GET":
						if($uri === "/"){
							$uri = "/index.html";
						}
						break;
					case "POST":
						$postData = explode("\n", $buffer);
						$postData = $postData[count($postData) - 1];
						foreach(explode("&", $postData) as $value){
							list($key, $value) = explode("=", $value);
							$_POST[$key] = $value;
						}

						$login = false;
						if(isset($_POST["username"]) && isset($_POST["password"])){
							if($_POST["username"] === $this->config->get("username") && $_POST["password"] === $this->config->get("password")){
								$login = true;
							}
						}

						echo $login;
						$content = implode("", $content);
						break;
				}

				if(!isset($content)){
					$content = @file_get_contents(__DIR__ . "/html" . $uri);
				}
				$statusCode = "200 OK";
				if(!$content){
					$statusCode = "404 Not Found";
					$content = "404 Not Found";
				}
				@fwrite($socket, "HTTP/1.1 " . $statusCode . "\nContent-Type: text/html \n\n" . $content);
				stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
				unset($content);
			}
		}
	}
}

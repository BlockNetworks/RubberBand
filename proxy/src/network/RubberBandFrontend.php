<?php

/**
 *
 * ______      _     _              ______                 _ 
 * | ___ \    | |   | |             | ___ \               | |
 * | |_/ /   _| |__ | |__   ___ _ __| |_/ / __ _ _ __   __| |
 * |    / | | | '_ \| '_ \ / _ \ '__| ___ \/ _` | '_ \ / _` |
 * | |\ \ |_| | |_) | |_) |  __/ |  | |_/ / (_| | | | | (_| |
 * \_| \_\__,_|_.__/|_.__/ \___|_|  \____/ \__,_|_| |_|\__,_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link https://github.com/PocketMine/RubberBand/
 * 
 *
*/

class RubberBandFrontend extends Thread{
	public $address, $port;
	private $socket;
	private $stop;
	private $workers;
	private $workerCount;
	public function __construct($address, $port){
		$this->address = $address;
		$this->port = $port;
		$this->start();
	}
	
	public function stop(){
		$this->stop = true;
	}
	
	public function addWorker(RubberBandReceiveWorker $worker){
		$this->workers[$this->workerCount] = $worker;
		++$this->workerCount;
	}

	public function run(){
		$this->stop = false;
		$this->workers = new StackableArray();
		
		$this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if(socket_bind($this->socket, $this->address, $this->port) === true){
			socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 0);
			socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, 65535);
			socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, 65535);
			socket_set_block($this->socket);
			console("[INFO] Started UDP frontend on {$this->address}:{$this->port}");
		}else{
			console("[ERROR] Couldn't start UDP socket on {$this->address}:{$this->port}");
		}

		$this->wait(); //Get ready for the action

		$buf = null;
		$source = null;
		$port = null;
		$count = 0;
		$packets = array();
		while($this->stop == false){
			if(($len = @socket_recvfrom($this->socket, $buf, 9216, 0, $source, $port)) > 0){
				$this->workers[$count]->processPacket(new RAWReceivedPacket($buf, $source, $port));
				++$count;
				if($count >= $this->workerCount){
					$count = 0;
				}
			}
		}
		return 0;
	}
	
}
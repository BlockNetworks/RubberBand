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

class StackableArray extends Stackable{
	public function __construct(){
		foreach(func_get_args() as $n => $value){
			if(is_array($value)){
				$this->{$n} = new StackableArray();
				call_user_func_array(array($this->{$n}, "__construct"), $value);
			}else{
				$this->{$n} = $value;
			}
		}
	}
	
	public function __unset($key){
		unset($this->{$key});
	}
	
	public function __destruct(){}
	
	public function run(){}
}
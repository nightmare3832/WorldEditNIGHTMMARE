<?php

namespace edit\command;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\command\Command;
use pocketmine\network\mcpe\protocol\types\CommandData;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\network\mcpe\protocol\types\CommandOriginData;
use pocketmine\network\mcpe\protocol\types\CommandOutputMessage;
use pocketmine\network\mcpe\protocol\types\CommandParameter;

class CommandManager {

	public static $SOTECommandData = [];

	public $command;
	public $data;

	public function __construct(Command $commands) {
		$this->command = $commands;
	}
    
	public function createCommandData($flags = 0, $permission = 0){
		$commandData = new CommandData();
		$commandData->commandName = $this->command->getName();
		$commandData->commandDescription = $this->command->getDescription();
		$commandData->flags = $flags;
		$commandData->permission = $permission;

		$this->data = $commandData;
	}

	public function setCommandParameter($args,$paramName,$paramType,$isOptional = true){
		$parameter = new CommandParameter();
		$parameter->paramName = $paramName;
		$parameter->paramType = $paramType;
		$parameter->isOptional = $isOptional;
		$this->data->overloads[$args][] = $parameter;
	}

	public function setSubCommand($args,$isOptional = true,array $subcommands = []){
		$paramName = "type";
		$paramType = 0x100000 | 0x11;
		$parameter = new CommandParameter();
		$parameter->paramName = $paramName;
		$parameter->paramType = $paramType;
		$parameter->isOptional = $isOptional;
		$enum = new CommandEnum();
		$enum->enumName = "{$args}";
		$enum->enumValues = $subcommands;
		$parameter->enum = $enum;
		$this->data->overloads[$args][] = $parameter;
	}

	public function setAliases(){
		$aliases = $this->command->getAliases();
		if(!empty($aliases)){
			$this->data->aliases = new CommandEnum();
			$this->data->aliases->enumName = ucfirst($this->command->getName()) . "Aliases";
			$this->data->aliases->enumValues = $aliases;
		}
	}

	public function register(){
		self::$SOTECommandData[$this->command->getName()] = $this->data;
	}

	public static function join(Player $player) {
		$pk = new AvailableCommandsPacket();
		foreach (self::$SOTECommandData as $name => $data) {
			$pk->commandData[$name] = $data;
		}
		$player->dataPacket($pk);
	}
}
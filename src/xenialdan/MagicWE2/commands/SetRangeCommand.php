<?php

declare(strict_types=1);

namespace xenialdan\MagicWE2\commands;

use ArgumentCountError;
use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Error;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use xenialdan\MagicWE2\exception\SessionException;
use xenialdan\MagicWE2\helper\SessionHelper;
use xenialdan\MagicWE2\Loader;

class SetRangeCommand extends BaseCommand
{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("range", true));
        $this->setPermission("we.command.setrange");
        $this->setUsage("//setrange [range: int]");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param BaseArgument[] $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $lang = Loader::getInstance()->getLanguage();
        if ($sender instanceof Player && SessionHelper::hasSession($sender)) {
            try {
                $lang = SessionHelper::getUserSession($sender)->getLanguage();
            } catch (SessionException $e) {
            }
        }
        try {
            if (empty($args["range"])) {
                $range = Loader::getInstance()->getToolDistance();
                $sender->sendMessage(Loader::PREFIX . TF::GREEN . $lang->translateString('command.setrange.current', [$range]));
            } else {
                Loader::getInstance()->getConfig()->set("tool-range", intval($args["range"]));
                $sender->sendMessage(Loader::PREFIX . TF::GREEN . $lang->translateString('command.setrange.set', [intval($args["range"])]));
            }
        } catch (Exception $error) {
            $sender->sendMessage(Loader::PREFIX . TF::RED . $lang->translateString('error.command-error'));
            $sender->sendMessage(Loader::PREFIX . TF::RED . $error->getMessage());
            $sender->sendMessage($this->getUsage());
        } catch (ArgumentCountError $error) {
            $sender->sendMessage(Loader::PREFIX . TF::RED . $lang->translateString('error.command-error'));
            $sender->sendMessage(Loader::PREFIX . TF::RED . $error->getMessage());
            $sender->sendMessage($this->getUsage());
        } catch (Error $error) {
            Loader::getInstance()->getLogger()->logException($error);
            $sender->sendMessage(Loader::PREFIX . TF::RED . $error->getMessage());
        }
    }
}

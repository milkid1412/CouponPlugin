<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin;

use JsonException;
use Kkevin14\CouponPlugin\command\MainCommand;
use Kkevin14\CouponPlugin\command\ManageCouponCommand;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class CouponPlugin extends PluginBase
{
    public Config $database;

    public array $db;

    public string $title = '§7[ §eCoupon §7]';

    public function onEnable(): void
    {
        $this->database = new Config($this->getDataFolder() . 'data.yml', Config::YAML, [
            'list' => [],
            'available' => []
        ]);
        $this->db = $this->database->getAll();
        $this->getServer()->getCommandMap()->registerAll('Kkevin14', [new ManageCouponCommand($this), new MainCommand($this)]);
    }

    public function msg(Player|string $player, string $msg)
    {
        if(!$player instanceof Player){
            $this->getServer()->getOfflinePlayer($player);
        }
        if($player->isOnline()){
            $player->sendMessage('§7[ §l§c! §r§7] §f' . $msg);
        }
    }

    /**
     * @throws JsonException
     */
    public function onDisable(): void
    {
        $this->database->setAll($this->db);
        $this->database->save();
    }
}
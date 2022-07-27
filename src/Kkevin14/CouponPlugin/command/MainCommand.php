<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\command;

use Kkevin14\CouponPlugin\CouponPlugin;
use Kkevin14\CouponPlugin\form\CouponEnterForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MainCommand extends Command
{
    public CouponPlugin $owner;

    public function __construct(CouponPlugin $owner)
    {
        parent::__construct('쿠폰', '쿠폰을 입력할 수 있습니다.', '/쿠폰', ['coupon']);
        $this->owner = $owner;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;
        $sender->sendForm(new CouponEnterForm($this->owner));
    }
}
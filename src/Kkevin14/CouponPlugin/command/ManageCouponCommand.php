<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\command;

use Kkevin14\CouponPlugin\CouponPlugin;
use Kkevin14\CouponPlugin\form\CouponManageForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ManageCouponCommand extends Command
{
    public CouponPlugin $owner;

    public function __construct(CouponPlugin $owner)
    {
        parent::__construct('쿠폰관리', '쿠폰을 관리합습니다.', '/쿠폰관리', ['couponmanage']);
        $this->owner = $owner;
        $this->setPermission('coupon.command.manage');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player || !$this->testPermission($sender)) return;
        $sender->sendForm(new CouponManageForm($this->owner));
    }
}
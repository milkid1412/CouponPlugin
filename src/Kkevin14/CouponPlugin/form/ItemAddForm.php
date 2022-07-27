<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ItemAddForm implements Form
{
    public CouponPlugin $owner;

    public function __construct(CouponPlugin $owner)
    {
        $this->owner = $owner;
    }

    #[ArrayShape(['type' => "string", 'title' => "string", 'content' => "\string[][]"])] public function jsonSerialize(): array
    {
        return [
            'type' => 'custom_form',
            'title' => $this->owner->title,
            'content' => [
                [
                    'type' => 'input',
                    'text' => '보상을 추가할 쿠폰번호를 입력해주세요(대/소문자는 자동으로 변환됩니다.)',
                    'placeholder' => 'ex) A34dF23BAS'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if(!isset($data[0])){
            $this->owner->msg($player, '쿠폰번호를 입력해주세요.');
            return;
        }
        $data[0] = strtolower($data[0]);
        foreach($this->owner->db['list'] as $key => $couponInfo) {
            if($couponInfo['code'] === $data[0]) $success = $key;
        }
        if(!isset($success)){
            $this->owner->msg($player, '쿠폰번호를 정확히 입력해주세요.');
            return;
        }
        $item = $player->getInventory()->getItemInHand();
        if($item->isNull()){
            $this->owner->msg($player, '공기는 보상으로 추가할 수 없습니다.');
            return;
        }
        $this->owner->db['list'][$success]['item'][] = $item->jsonSerialize();
        $this->owner->msg($player, '손에 들고 있는 아이템이 보상에 추가되었습니다.');
    }
}
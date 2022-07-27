<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CouponManageForm implements Form
{
    public CouponPlugin $owner;

    public function __construct(CouponPlugin $owner)
    {
        $this->owner = $owner;
    }

    #[ArrayShape(['type' => "string", 'title' => "string", 'content' => "string", 'buttons' => "\string[][]"])] public function jsonSerialize(): array
    {
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => "\n" . '원하시는 작업을 선택해주세요.' . "\n",
            'buttons' => [
                [
                    'text' => '§f[ §e쿠폰 생성하기 §f]'
                ],
                [
                    'text' => '§f[ §e쿠폰 보상(아이템) 추가 §f]'
                ],
                [
                    'text' => '§f[ §e쿠폰 보상(돈) 추가 §f]'
                ],
                [
                    'text' => '§f[ §e쿠폰 활성화하기 §f]'
                ],
                [
                    'text' => '§f[ §e쿠폰 목록 §f]'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        if($data === 0){
            $player->sendForm(new CreateCouponForm($this->owner));
        }elseif($data === 1){
            $player->sendForm(new ItemAddForm($this->owner));
        }elseif($data === 2){
            $player->sendForm(new SetMoneyForm($this->owner));
        }elseif($data === 3){
            $player->sendForm(new EnableCouponForm($this->owner));
        }else{
            $player->sendForm(new CouponListForm($this->owner));
        }
    }
}
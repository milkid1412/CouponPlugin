<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CreateCouponForm implements Form
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
                    'text' => '쿠폰번호를 입력해주세요(대/소문자는 자동으로 변환됩니다.)',
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
        foreach($this->owner->db['list'] as $couponInfo) {
            if ($couponInfo['code'] === $data[0]) $fail = 1;
        }
        if(isset($fail)){
            $this->owner->msg($player, $data[0] . '이라는 쿠폰번호는 이미 존재합니다.');
            return;
        }
        $this->owner->db['list'][] = [
            'code' => $data[0],
            'money' => 0,
            'item' => []
        ];
        $this->owner->msg($player, '성공적으로 쿠폰을 생성했습니다.');
    }
}
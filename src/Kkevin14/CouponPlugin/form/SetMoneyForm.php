<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SetMoneyForm implements Form
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
                ],
                [
                    'type' => 'input',
                    'text' => '쿠폰을 입력하면 받을 액수를 입력해주세요.',
                    'placeholder' => 'ex) 100000'
                ],
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if(!isset($data[0]) || !isset($data[1]) || !is_numeric($data[1])){
            $this->owner->msg($player, '쿠폰번호와 돈을 정확히 입력해주세요.');
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
        $this->owner->db['list'][$success]['money'] = (int) $data[1];
        $this->owner->msg($player, $data[1] . '원이 보상으로 설정되었습니다.');
    }
}
<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use pocketmine\form\Form;
use pocketmine\player\Player;

class EnableCouponForm implements Form
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
                    'type' => 'label',
                    'text' => '쿠폰의 유효기간을 적을 때는 반드시 분 단위로 입력해주세요.' . "\n" . '지금 시간 + 아래에 입력한 시간(분)이 지나면 해당 쿠폰을 사용할 수 없게 됩니다.'
                ],
                [
                    'type' => 'input',
                    'text' => '유효기간을 적어주세요' . "\n\n" . '무기한: -1분' . "\n" . '1시간: 60분' . "\n" . '6시간: 360분' . "\n" . '12시간: 720분' . "\n" . '하루: 1440분' . "\n" . '일주일: 10080분',
                    'placeholder' => 'ex) 1440'
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
        if(!isset($data[2]) || !is_numeric($data[2]) || (int) $data[2] < -1 || (int) $data[2] > PHP_INT_MAX){
            $this->owner->msg($player, '기간은 -1~' . PHP_INT_MAX . '사이로 적어주세요.');
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
        $time_limit = ((int) $data[2] === -1) ? -1 : time() + (int) $data[2] * 60;
        $this->owner->db['available'][] = [
            'code' => $data[0],
            'time_limit' => $time_limit,
            'list_number' => $success,
            'used' => []
        ];
        $this->owner->msg($player, '쿠폰이 활성화되었습니다. 쿠폰번호를 입력하여 확인해보세요.');
        $this->owner->msg($player, '쿠폰을 제거하기 위해서는 서버를 완전히 종료한 후 plugin_data\CouponPlugin\data.yml의 available에서 해당 부분을 지워주셔야 합니다. 만약 처리가 힘들 경우 제작자에게 연락해주세요.');
        $this->owner->msg($player, '제작자 연락처: kevin_shop(카카오톡)');
    }
}
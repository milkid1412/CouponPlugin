<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use onebone\economyapi\EconomyAPI;
use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\player\Player;

class CouponEnterForm implements Form
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
                    'type' => 'label',
                    'text' => '쿠폰번호를 입력하기 전에 인벤토리에 충분한 공간이 있는지 확인해주세요.' . "\n" . '만약 보상을 받지 못해도 책임지지 않습니다.'
                ],
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
        if(!isset($data[1])){
            $this->owner->msg($player, '쿠폰번호를 입력해주세요.');
            return;
        }
        $data[1] = strtolower($data[1]);
        foreach($this->owner->db['available'] as $key => $couponInfo) {
            if($couponInfo['code'] === $data[1]) $success = [$couponInfo['list_number'], $key];
        }
        if(!isset($success)){
            $this->owner->msg($player, '존재하지 않는 쿠폰번호이거나 유효기간이 지난 쿠폰번호입니다.');
            return;
        }
        if($this->owner->db['available'][$success[1]]['time_limit'] > 0 && $this->owner->db['available'][$success[1]]['time_limit'] < time()){
            $this->owner->msg($player, '해당 쿠폰의 입력 기간이 지났습니다.');
            unset($this->owner->db['available'][$success[1]]);
            return;
        }
        if(in_array($player->getName(), $this->owner->db['available'][$success[1]]['used'])){
            $this->owner->msg($player, '이미 해당 쿠폰번호를 사용했습니다.');
            return;
        }
        $couponInfo = $this->owner->db['list'][$success[0]];
        foreach($couponInfo['item'] as $item){
            $player->getInventory()->addItem(Item::jsonDeserialize($item));
        }
        $this->owner->db['available'][$success[1]]['used'][] = $player->getName();
        EconomyAPI::getInstance()->addMoney($player, (int) $couponInfo['money']);
        $this->owner->msg($player, '쿠폰보상이 지급되었습니다.');
    }
}
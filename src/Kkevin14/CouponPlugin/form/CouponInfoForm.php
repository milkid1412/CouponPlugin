<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\player\Player;

class CouponInfoForm implements Form
{
    public CouponPlugin $owner;

    public int $key;

    public function __construct(CouponPlugin $owner, int $key)
    {
        $this->owner = $owner;
        $this->key = $key;
    }

    #[ArrayShape(['type' => "string", 'title' => "string", 'content' => "string", 'buttons' => "\string[][]"])] public function jsonSerialize(): array
    {
        $item_list = '|| ';
        foreach($this->owner->db['list'][$this->key]['item'] as $item){
            $item_ = Item::jsonDeserialize($item);
            $itemName = $item_->hasCustomName() ? $item_->getCustomName() : $item_->getName();
            $item_list .= $itemName . ' X' . $item_->getCount() . ' ||';
        }
        return [
            'type' => 'custom_form',
            'title' => $this->owner->title,
            'content' => [
                [
                    'type' => 'label',
                    'text' => '돈: ' . $this->owner->db['list'][$this->key]['money'] . '원' . "\n\n" . '아이템: ' . $item_list
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        //
    }
}
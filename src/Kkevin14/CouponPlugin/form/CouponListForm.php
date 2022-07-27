<?php
declare(strict_types=1);

namespace Kkevin14\CouponPlugin\form;

use JetBrains\PhpStorm\ArrayShape;
use Kkevin14\CouponPlugin\CouponPlugin;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CouponListForm implements Form
{
    public CouponPlugin $owner;

    public function __construct(CouponPlugin $owner)
    {
        $this->owner = $owner;
    }

    #[ArrayShape(['type' => "string", 'title' => "string", 'content' => "string", 'buttons' => "\string[][]"])] public function jsonSerialize(): array
    {
        $buttons = [];
        foreach($this->owner->db['list'] as $key => $value){
            $buttons[] = [
                'text' => '[ ' . $value['code'] . ' ]'
            ];
        }
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => "\n" . '원하시는 작업을 선택해주세요.' . "\n",
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $player->sendForm(new CouponInfoForm($this->owner, $data));
    }
}
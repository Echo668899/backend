<?php

namespace App\Services\Activity\Handler;

/**
 * 倒计时活动
 * 展示型活动
 */
class CountdownHandler extends BaseHandler
{
    public function code(): string
    {
        return 'countdown';
    }

    public function name(): string
    {
        return '倒计时活动';
    }

    public function description(): string
    {
        return '倒计时，纯文字展示,无交互,类似广告';
    }

    public function schema(): array
    {
        return [
            // 显示时长
            'is_show_time' => [
                'type'    => 'radio',
                'label'   => '显示倒计时',
                'options' => [
                    [
                        'code'        => 'y',
                        'name'        => '显示',
                        'description' => '',
                    ],
                    [
                        'code'        => 'n',
                        'name'        => '隐藏',
                        'description' => '',
                    ],
                ],
                'default' => 'y',
            ],
            // 链接
            'link' => [
                'type'    => 'text',
                'default' => '',
                'label'   => '链接',
            ],
        ];
    }

    /**
     * 展示型活动,无需执行
     * @param  int   $userId
     * @param  array $activityRow
     * @return void
     */
    public function execute(int $userId, array $activityRow)
    {
    }
}

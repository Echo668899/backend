<?php

namespace App\Core\Services;

use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;

class LocaleService
{
    protected string $lang             = 'zh';
    protected ?NativeArray $translator = null;

    public function setLang(string $lang): void
    {
        if ($this->lang !== $lang) {
            $this->lang       = $lang;
            $this->translator = null; // 语言变更时重置
        }
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getTranslator(): NativeArray
    {
        if ($this->translator !== null) {
            return $this->translator;
        }

        $interpolator = new InterpolatorFactory();

        $this->translator = new NativeArray(
            $interpolator,
            [
                'content'      => $this->loadFile(),
                'triggerError' => false,
            ]
        );

        return $this->translator;
    }

    protected function loadFile(): array
    {
        $file = APP_PATH . "/Resource/lang/{$this->lang}.php";

        if (!file_exists($file)) {
            return [];
        }

        return include $file;
    }
}

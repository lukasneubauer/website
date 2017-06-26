<?php

namespace App\Videos;

use App\Exceptions\InvalidVideoUrlException;
use Nette\Localization\ITranslator;
use Nette\Utils\Strings;

class Youtube
{
    /** @var ITranslator */
    private $translator;

    /**
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param  string                   $pageUrl
     * @throws InvalidVideoUrlException
     * @return string
     */
    public function getVideoSrc($pageUrl)
    {
        $key = 'watch?v=';

        if (!Strings::contains($pageUrl, $key)) {
            throw new InvalidVideoUrlException(
                $this->translator->translate('locale.error.invalid_youtube_video_url')
            );
        }

        $embedUrl = str_replace($key, 'embed/', $pageUrl);
        if (!Strings::contains($embedUrl, '&')) {
            return $embedUrl;
        }

        return Strings::before($embedUrl, '&');
    }
}

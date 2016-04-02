<?php

namespace App\AdminModule\Presenters;

use App\Components\Forms;

final class SettingsPresenter extends SecurePresenter
{
    /** @var Forms\ProfileSettingsFormInterface @inject */
    public $profileSettingsForm;

    /**
     * @return Forms\ProfileSettingsForm
     */
    protected function createComponentProfileSettingsForm()
    {
        return $this->profileSettingsForm->create($this->getLoggedUserEntity());
    }
}

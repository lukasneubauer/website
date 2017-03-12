<?php

namespace App\Front;

use App;
use App\Components;
use App\Forms;
use Nette\Mail\IMailer;

abstract class BasePresenter extends App\Presenters\BasePresenter
{
    /** @var Components\MenuControlInterface @inject */
    public $menuControl;

    /** @var Forms\SignUpFormInterface @inject */
    public $signUpForm;

    /** @var Forms\SignInFormInterface @inject */
    public $signInForm;

    /** @var Forms\SignResetFormInterface @inject */
    public $signResetForm;

    /** @var IMailer @inject */
    public $mailer;

    /** @var bool */
    protected $canAccess = false;

    /** @var string */
    protected $appDir;

    /** @var string */
    protected $contactEmail;

    protected function startup()
    {
        parent::startup();

        $parameters = $this->context->parameters;

        $this->appDir       = $parameters['appDir'];
        $this->contactEmail = $parameters['contactEmail'];

        $this->registerFormExtendingMethods();
    }

    /**
     * @return Components\MenuControlInterface
     */
    protected function createComponentMenuControl()
    {
        return $this->menuControl->create();
    }

    /**
     * @return Forms\SignUpForm
     */
    protected function createComponentSignUpForm()
    {
        return $this->signUpForm->create($this->getHttpRequest()->getUrl(), $this->appDir, $this->contactEmail);
    }

    /**
     * @return Forms\SignInForm
     */
    protected function createComponentSignInForm()
    {
        return $this->signInForm->create();
    }

    /**
     * @return Forms\SignResetForm
     */
    protected function createComponentSignResetForm()
    {
        return $this->signResetForm->create($this->appDir, $this->contactEmail);
    }
}
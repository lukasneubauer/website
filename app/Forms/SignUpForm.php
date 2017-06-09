<?php

namespace App\Forms;

use App\Duplicities\PossibleUniqueKeyDuplicationException;
use App\Emails\AccountActivationEmail;
use App\Exceptions\FormSentBySpamException;
use App\Links\AccountActivationLinkGenerator;
use App\Repositories;
use App\Utils\FlashType;
use Latte;
use Nette\Application\UI\Form;
use Nette\Application\UI\ITemplate;
use Nette\Http\UrlScript;
use Nette\Localization\ITranslator;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Utils\ArrayHash;

class SignUpForm extends AbstractForm
{
    /** @var AccountActivationEmail */
    private $accountActivationEmail;

    /** @var AccountActivationLinkGenerator */
    private $accountActivationLinkGenerator;

    /** @var Repositories\UserRepository */
    private $userRepository;

    /** @var IMailer */
    private $mailer;

    /** @var UrlScript */
    private $urlScript;

    /** @var string */
    private $appDir;

    /** @var string */
    private $contactEmail;

    /**
     * @param AccountActivationEmail         $accountActivationEmail
     * @param AccountActivationLinkGenerator $accountActivationLinkGenerator
     * @param ITranslator                    $translator
     * @param Repositories\UserRepository    $userRepository
     * @param IMailer                        $mailer
     * @param UrlScript                      $urlScript
     * @param string                         $appDir
     * @param string                         $contactEmail
     */
    public function __construct(
        AccountActivationEmail $accountActivationEmail,
        AccountActivationLinkGenerator $accountActivationLinkGenerator,
        ITranslator $translator,
        Repositories\UserRepository $userRepository,
        IMailer $mailer,
        UrlScript $urlScript,
        $appDir,
        $contactEmail
    ) {
        parent::__construct($translator);

        $this->accountActivationEmail         = $accountActivationEmail;
        $this->accountActivationLinkGenerator = $accountActivationLinkGenerator;
        $this->userRepository                 = $userRepository;
        $this->mailer                         = $mailer;
        $this->urlScript                      = $urlScript;
        $this->appDir                         = $appDir;
        $this->contactEmail                   = $contactEmail;
    }

    protected function configure(Form $form)
    {
        $form->addText('username', 'locale.form.username')
            ->setRequired('locale.form.username_required');

        $form->addText('email', 'locale.form.email')
            ->addRule($form::EMAIL, 'locale.form.email_not_in_order')
            ->setRequired('locale.form.email_address');

        $form->addText('forename', 'locale.form.forename');

        $form->addText('surname', 'locale.form.surname');

        $form->addPassword('password', 'locale.form.password')
            ->setRequired('locale.form.password_required');

        $form->addPassword('password_confirm', 'locale.form.password_confirm')
            ->addRule($form::EQUAL, 'locale.form.password_equal', $form['password'])
            ->setRequired('locale.form.password_confirm_required')
            ->setOmitted();

        $form->addText('__anti', '__Anti', null)
            ->setAttribute('style', 'display: none;');

        $form->addSubmit('submit', 'locale.form.submit_sign_up');
    }

    public function formSucceeded(Form $form)
    {
        try {
            $p      = $this->getPresenter();
            $values = $form->getValues();

            $this->checkSpam($values);

            $user = $this->userRepository->createRegistration($values);
            /*$link = $p->link(
                '//:Admin:Sign:unlock',
                [
                    'uid'   => $user->id,
                    'token' => $user->token,
                ]
            );*/

            $this->sendEmail(
                $this->contactEmail,
                $user->email,
                $this->translator->translate('locale.sign.sign_up_request'),
                $link
            );

            $p->flashMessage(
                $this->translator->translate('locale.sign.sign_up_email_sent'),
                FlashType::SUCCESS
            );
        } catch (FormSentBySpamException $e) {
            $this->addFormError($form, $e);
            $this->redrawControl('formErrors');
        } catch (PossibleUniqueKeyDuplicationException $e) {
            $this->addFormError($form, $e);
            $this->redrawControl('formErrors');
        } catch (\Exception $e) {
            $this->addFormError(
                $form,
                $e,
                $this->translator->translate('locale.error.occurred')
            );
            $this->redrawControl('formErrors');
        }

        if (!empty($user)) {
            $p->redirect('Homepage:default');
        }
    }

    protected function insideRender(ITemplate $template)
    {
        $template->form = $this->form;
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $link
     */
    private function sendEmail($from, $to, $subject, $link)
    {
        $latte = new Latte\Engine;

        $parameters = [
            'subject' => $subject,
            'link'    => $link,
            'baseUri' => $this->urlScript->getHostUrl(),
            'host'    => $this->urlScript->getHost(),
        ];

        $email = new Message;
        $email->setFrom($from)
            ->addTo($to)
            ->setSubject($subject)
            ->setHtmlBody(
                $latte->renderToString(
                    $this->appDir . '/Presenters/templates/emails/registration.latte',
                    $parameters
                )
            );

        $this->mailer->send($email);
    }

    /**
     * @param  ArrayHash $values
     * @throws FormSentBySpamException
     */
    private function checkSpam(ArrayHash $values)
    {
        if (strlen($values->__anti) > 0) {
            throw new FormSentBySpamException(
                $this->translator->translate('locale.form.spam_attempt_sign_up')
            );
        }
        unset($values->__anti);
    }
}

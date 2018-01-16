<?php
namespace App\Account\Action;

use App\Auth\UserTable;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class AccountEditAction
{


    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * @var FlashService
     */
    private $flashService;


    public function __construct(
        RendererInterface $renderer,
        Auth $auth,
        UserTable $userTable,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->userTable = $userTable;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->getUser();
        $validator = (new Validator($params))
            ->required('firstname', 'lastname')
            ->length('firstname', 2)
            ->length('lastname', 2)
            ->confirm('password')
            ->email('email');
        if ($validator->isValid()) {
            $userParams = [
                'firstname' => $params['firstname'],
                'lastname' => $params['lastname']
            ];
            if (!empty($params['password'])) {
                $userParams = [
                    'password' => password_hash($params['password'], PASSWORD_DEFAULT)
                ];
            }
            if (!empty($params['email']) && $params['email'] !== $user->email) {
                $validator = (new Validator($params))
                    ->unique('email', $this->userTable);
                if ($validator->isValid()) {
                    $userParams = [
                        'email' => $params['email']
                    ];
                } else {
                    $errors = $validator->getErrors();
                    return $this->renderer->render('@account/account', compact('user', 'errors'));
                }
            }
            $this->userTable->update($user->id, $userParams);
            $this->flashService->success('Vos informations ont bien été modifiées');
            return new RedirectResponse($request->getUri()->getPath());
        }
        $errors = $validator->getErrors();
        return $this->renderer->render('@account/account', compact('user', 'errors'));
    }
}

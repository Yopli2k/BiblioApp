<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace BiblioApp\Controller;

use BiblioApp\Core\App\AppCookies;
use BiblioApp\Core\App\PageController;
use BiblioApp\Model\User;
use Symfony\Component\HttpFoundation\Response;

class LoginUser extends PageController
{

    /**
     * Runs the controller's logic.
     *
     * @param Response $response
     */
    public function exec(Response &$response): void
    {
        parent::exec($response);

        // Get action and execute if not empty
        $action = $this->request->request->get('action', $this->request->query->get('action', ''));
        if (false === $this->execPreviousAction($action)) {
            $this->setTemplate(false);
        }
    }

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Bibliotecario';
        $data['breadcrumb'] = 'Identificarse';
        return $data;
    }

    /**
     * Run the actions that alter data before reading it.
     *
     * @param ?string $action
     * @return bool
     */
    protected function execPreviousAction(?string $action): bool
    {
        switch ($action) {
            case 'logout':
                AppCookies::clear($this->response);
                $this->redirect('LoginUser');
                return false;

            case 'login':
                if ($this->loginAction()) {
                    $this->redirect('ListUser');
                    return false;
                }
                return true;

            case 'change-password':
                if($this->changePasswordAction()) {
                    $this->redirect('LoginUser');
                    return false;
                }
                return true;
        }
        return true;
    }

    /**
     * make login for user.
     *
     * @return bool
     */
    private function loginAction(): bool
    {
        $data = $this->request->request->all();
        $user = new User();
        if ($user->loadFromCode($data['biblioUserName'])
            && $user->enabled
            && $user->verifyPassword($data['biblioPassword'])
        ) {
            $user->newLogkey();
            AppCookies::update($this->response, $user);
            return true;
        }

        return false;
    }

    /**
     * Set new password for user.
     *
     * @return bool
     */
    private function changePasswordAction(): bool
    {
        $data = $this->request->request->all();
        $user = new User();
        if ($user->loadFromCode($data['username'])
            && $user->enabled
            && $data['admin_passwd'] === APP_DB_PASS
            && $data['new_passwd'] === $data['confirm_passwd']
        ) {
            $user->setPassword($data['new_passwd']);
            $user->save();
            return true;
        }

        return false;
    }
}
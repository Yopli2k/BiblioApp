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
use BiblioApp\Core\Controller\FrontPageController;
use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Model\Member;
use Symfony\Component\HttpFoundation\Response;

class LoginMember extends FrontPageController
{

    /**
     * Runs the controller's logic.
     * if return false, the controller break the execution.
     *
     * @param Response $response
     * @return bool
     */
    public function exec(Response &$response): bool
    {
        if (false === parent::exec($response)) {
            return false;
        }

        // Get action and execute if not empty
        $action = $this->request->request->get('action', $this->request->query->get('action', ''));
        if (false === $this->execPreviousAction($action)) {
            $this->setTemplate(false);
            return false;
        }
        return true;
    }

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Login';
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
            case 'login':
                if ($this->loginAction()) {
                    $this->redirect('/index.php');
                    return false;
                }
                $this->message->error('Usuario o contraseña incorrectos.');
                break;

            case 'change-password':
                if($this->changePasswordAction()) {
                    $this->redirect('LoginUser');
                    return false;
                }
                $this->message->error('Error cambiando la contraseña.');
                break;
        }
        return true;
    }

    /**
     * make login for member.
     *
     * @return bool
     */
    private function loginAction(): bool
    {
        $data = $this->request->request->all();
        $where = [ new DataBaseWhere('email', $data['email']) ];
        $member = new Member();
        if ($member->loadFromCode('', $where)
            && $member->enabled
            && $member->verifyPassword($data['password'])
        ) {
            $member->newLogkey();
            AppCookies::setCookie($this->response, 'biblioMemberID', $member->id);
            AppCookies::setCookie($this->response, 'biblioMemberLogKey', $member->logkey);
            return true;
        }

        return false;
    }
}
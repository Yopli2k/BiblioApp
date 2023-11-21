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

use BiblioApp\Core\Controller\FrontPageController;
use Symfony\Component\HttpFoundation\Response;

class MemberPanel extends FrontPageController
{

    /**
     * Runs the controller's logic.
     * if return false, the controller break the execution.
     * if member is not logged in, redirect to home page.
     *
     * @param Response $response
     * @return bool
     */
    public function exec(Response &$response): bool
    {
        if (false === parent::exec($response)) {
            return false;
        }

        if (false === isset($this->member) || empty($this->member->id)) {
            $this->redirect('');
            $this->setTemplate(false);
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
        $data['title'] = 'Mi Información';
        $data['breadcrumb'] = 'Perfil';
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
        if ($action == 'update') {
            if ($this->updateAction()) {
                $this->message->info('Información actualizada correctamente.');
            } else {
                $this->message->error('No se ha podido actualizar los datos.');
            }
        }
        return true;
    }

    private function updateAction(): bool
    {
        $data = $this->request->request->all();
        $this->member->address = $data['address'];
        $this->member->document = $data['document'];
        $this->member->name = $data['name'];
        $this->member->phone = $data['phone'];
        if ($this->member->checkNewEmail($data['email'])) {
            $this->member->email = $data['email'];
        }
        return $this->member->save();
    }
}
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

use BiblioApp\Core\App\PageController;
use BiblioApp\Model\WebContact;
use Symfony\Component\HttpFoundation\Response;

class Contact extends PageController
{

    /**
     * Runs the controller's logic.
     *
     * @param Response $response
     */
    public function exec(Response &$response): void
    {
        parent::exec($response);
        $action = $this->request->request->get('action', $this->request->query->get('action', ''));
        $this->execPreviousAction($action);
    }

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Contactar con Nosotros';
        $data['breadcrumb'] = 'Contactar';
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
        if ($action == 'contact') {
            $results = $this->contactAction();
            $this->response->setContent(json_encode($results));
            return false;
        }

        return true;
    }

    private function contactAction(): array
    {
        $data = $this->request->request->get();
        $contact = new WebContact();
        $contact->email = $data['email'] ?? '';
        $contact->name = $data['name'] ?? '';
        $contact->notes = $data['notes'] ?? '';
        $contact->phone = $data['phone'] ?? '';

        if ($contact->save()) {
            return [
                'result' => true,
                'message' => 'Mensaje enviado correctamente'
            ];
        }

        return [
            'result' => false,
            'message' => 'No se ha podido registrar el mensaje'
        ];
    }
}
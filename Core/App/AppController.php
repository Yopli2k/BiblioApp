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
namespace BiblioApp\Core\App;

use Exception;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class to manage selected controller.
 *
 * @author Carlos García Gómez <carlos@facturascripts.com>
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class AppController extends AppBase
{
    const USER_UPDATE_ACTIVITY_PERIOD = 3600;

    /**
     * Controller loaded
     *
     * @var Controller
     */
    private $controller = null;

    /**
     * Contains the page name.
     *
     * @var string
     */
     private $pageName = '';

    /**
     * Initializes the app.
     *
     * @param string $uri
     * @param string $pageName
     */
    public function __construct(string $uri = '/', string $pageName = '')
    {
        parent::__construct($uri);
        $this->pageName = $pageName;
    }

    /**
     * Select and run the corresponding controller.
     *
     * @return bool
     */
    public function run(): bool
    {
        if (false === parent::run()) {
            return false;
        }

        $pageName = $this->getPageName();
        $this->loadController($pageName);
        return true;
    }

    /**
     * Finalize the app.
     *
     * @param int $status
     * @param string $message
     */
    protected function die(int $status, string $message = ''): void
    {
        $this->response->setContent(nl2br($content));
        $this->response->setStatusCode($status);
    }

    /**
     * Returns the name of the default controller for the current user or for all users.
     *
     * @return string
     */
    private function getPageName(): string
    {
        if (false === empty($this->pageName)) {
            return $this->pageName;
        }

        if (false === empty($this->getUriParam(0)) && $this->getUriParam(0) !== 'index.php') {
            return $this->getUriParam(0);
        }

        return 'Home';
    }

    /**
     * Load and process the $pageName controller.
     *
     * @param string $pageName
     */
    protected function loadController(string $pageName): void
    {
        $controllerName = $this->getControllerFullName($pageName);
        if (false === class_exists($controllerName)) {
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->renderHtml('Error/ControllerNotFound.html.twig', $controllerName);
            return;
        }

        $this->controller = new $controllerName($pageName, $this->uri);
        $this->controller->exec($this->response);
        $template = $this->controller->getTemplate();
        $this->renderHtml($template, $controllerName);
    }

    /**
     * Creates HTML with the selected template. The data will not be inserted in it
     * until render() is executed
     *
     * @param string $template
     * @param string $controllerName
     */
    protected function renderHtml(string $template, string $controllerName = ''): void
    {
        // HTML template variables
        $templateVars = [
            'controllerName' => $controllerName,
            'controller' => $this->controller,
            'template' => $template
        ];

        try {
            $this->response->setContent(Html::render($template, $templateVars));
        } catch (Exception $exc) {
            // ToolBox::log()->critical($exc->getMessage());
            $this->response->setContent(Html::render('Error/TemplateError.html.twig', $templateVars));
            $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Returns the controllers full name
     *
     * @param string $pageName
     *
     * @return string
     */
    private function getControllerFullName(string $pageName): string
    {
        return '\\' . APP_NAME . '\\Controller\\' . $pageName;
    }
}

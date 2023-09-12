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

use BiblioApp\Core\DataBase\DataBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class from which all web pages controllers must inherit.
 *
 * @author Carlos García Gómez <carlos@facturascripts.com>
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
abstract class PageController
{
    /**
     * It provides direct access to the database.
     *
     * @var DataBase
     */
    protected $dataBase;

    /**
     * Given uri, default is empty.
     *
     * @var string
     */
    protected $uri;

    /**
     * HTTP Response object.
     *
     * @var Response
     */
    protected $response;

    /**
     * Request on which we can get data.
     *
     * @var Request
     */
    protected $request;

    /**
     * Name of the class of the controller (although its in inheritance from this class,
     * the name of the final class we will have here)
     *
     * @var string __CLASS__
     */
    private $className;

    /**
     * Name of the file for the template.
     *
     * @var string|false template.html.twig
     */
    private $template;

    /**
     * Initialize all objects and properties.
     *
     * @param string $className
     * @param string $uri
     */
    public function __construct(string $className, string $uri = '')
    {
        $this->className = $className;
        $this->dataBase = new DataBase();
        $this->request = Request::createFromGlobals();
        $this->template = $this->className . '.html.twig';
        $this->uri = $uri;
    }

    /**
     * Return the basic data for this page.
     *
     * @return array
     */
    public function getPageData(): array
    {
        return [
            'name' => $this->className,
            'title' => $this->className,
        ];
    }

    /**
     * Return the template to use for this controller.
     *
     * @return string|false
     */
    public function getTemplate(): string|false
    {
        return $this->template;
    }

    /**
     * Return the title for web page.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getPageData()['title'] ?? $this->className;
    }

    /**
     * Execute the public part of the controller.
     *
     * @param Response $response
     */
    public function exec(Response &$response)
    {
        $this->response = &$response;
    }

    /**
     * Redirect to an url or controller.
     *
     * @param string $url
     * @param int $delay
     */
    public function redirect(string $url, int $delay = 0)
    {
        $this->response->headers->set('Refresh', $delay . '; ' . $url);
        if ($delay === 0) {
            $this->setTemplate(false);
        }
    }

    /**
     * Set the template to use for this controller.
     *
     * @param string|false $template
     * @return bool
     */
    public function setTemplate(string|false $template): bool
    {
        $this->template = ($template === false) ? false : $template . '.html.twig';
        return true;
    }

    /**
     * Return the URL of the actual controller.
     *
     * @return string
     */
    public function url(): string
    {
        return $this->className;
    }

    /**
     * Return the name of the controller.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->className;
    }
}

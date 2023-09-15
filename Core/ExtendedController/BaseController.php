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
namespace BiblioApp\Core\ExtendedController;

use BiblioApp\Core\App\PageController;
use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Model\CodeModel;
use BiblioApp\Model\User;
use Symfony\Component\HttpFoundation\Response;

/*
use FacturaScripts\Core\Lib\Widget\VisualItem;
use FacturaScripts\Core\Model\Base\ModelClass;
use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Lib\ExportManager;
*/

/**
 * Base controller for all extended controllers.
 *
 * @author Carlos García Gómez <carlos@facturascripts.com>
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
abstract class BaseController extends PageController
{
    const MODEL_NAMESPACE = '\\BiblioApp\\Model\\';

    /**
     * Indicates the active view.
     *
     * @var string
     */
    public string $active;

    /**
     * Model to use with select and autocomplete filters.
     *
     * @var CodeModel
     */
    public CodeModel $codeModel;

    /**
     * List of views displayed by the controller.
     *
     * @var BaseView[]|ListView[]
     */
    public array $views = [];

    /**
     * Inserts the views or tabs to display.
     */
    abstract protected function createViews(): void;

    /**
     * Loads the data to display.
     *
     * @param string $viewName
     * @param BaseView $view
     */
    abstract protected function loadData(string $viewName, mixed $view): void;

    /**
     * Initializes all the objects and properties.
     *
     * @param string $className
     * @param ?User $user
     * @param string $uri
     */
    public function __construct(string $className, ?User $user, string $uri = '')
    {
        parent::__construct($className, $user, $uri);
        $activeTabGet = $this->request->query->get('activetab', '');
        $this->active = $this->request->request->get('activetab', $activeTabGet);
        $this->codeModel = new CodeModel();
    }

    /**
     * @param string $viewName
     * @param BaseView|ListView $view
     */
    public function addCustomView(string $viewName, mixed $view): void
    {
        if ($viewName !== $view->getViewName()) {
            // Tools::log()->error('$viewName must be equals to $view->name');
            return;
        }

        $view->loadPageOptions();
        $this->views[$viewName] = $view;
        if (empty($this->active)) {
            $this->active = $viewName;
        }
    }

    /**
     * Runs the controller's logic.
     *
     * @param Response $response
     */
    public function exec(Response &$response): void
    {
        parent::exec($response);
        $this->createViews();
    }

    /**
     * Returns the configuration value for the indicated view.
     *
     * @param string $viewName
     * @param string $property
     *
     * @return mixed
     */
    public function getSettings(string $viewName, string $property): mixed
    {
        if (isset($this->views[$viewName])) {
            return $this->views[$viewName]->settings[$property] ?? null;
        }

        return null;
    }

    /**
     * Set value for setting of a view
     *
     * @param string $viewName
     * @param string $property
     * @param mixed $value
     */
    public function setSettings(string $viewName, string $property, mixed $value): void
    {
        if (isset($this->views[$viewName])) {
            $this->views[$viewName]->settings[$property] = $value;
        }
    }

    /**
     * Run the autocomplete action.
     * Returns a JSON string for the searched values.
     *
     * @return array
     */
    protected function autocompleteAction(): array
    {
        $data = $this->requestGet(['field', 'fieldcode', 'fieldfilter', 'fieldtitle', 'formname', 'source', 'strict', 'term']);
        if ($data['source'] == '') {
            return $this->getAutocompleteValues($data['formname'], $data['field']);
        }

        $where = [];
        foreach (DataBaseWhere::applyOperation($data['fieldfilter'] ?? '') as $field => $operation) {
            $value = $this->request->get($field);
            $where[] = new DataBaseWhere($field, $value, '=', $operation);
        }

        // FIXME: TOOLS dont exist
        $results = [];
        foreach ($this->codeModel->search($data['source'], $data['fieldcode'], $data['fieldtitle'], $data['term'], $where) as $value) {
            $results[] = ['key' => Tools::fixHtml($value->code), 'value' => Tools::fixHtml($value->description)];
        }

        if (empty($results) && '0' == $data['strict']) {
            $results[] = ['key' => $data['term'], 'value' => $data['term']];
        } elseif (empty($results)) {
            $results[] = ['key' => null, 'value' => Tools::lang()->trans('no-data')];
        }

        return $results;
    }


    // FIXME: review this method
    /**
     * Action to delete data.
     *
     * @return bool
     */
    protected function deleteAction(): bool
    {
        // check user permissions
        if (false === $this->views[$this->active]->settings['btnDelete']) {
            return false;
        }

        $model = $this->views[$this->active]->model;
        $codes = $this->request->request->get('code', '');
        if (empty($codes)) {
            return false;
        }

        if (is_array($codes)) {
            $this->dataBase->beginTransaction();

            // deleting multiples rows
            $numDeletes = 0;
            foreach ($codes as $cod) {
                if ($model->loadFromCode($cod) && $model->delete()) {
                    ++$numDeletes;
                    continue;
                }
                $this->dataBase->rollback();
                break;
            }

            $model->clear();
            $this->dataBase->commit();
            if ($numDeletes > 0) {
                // Tools::log()->notice('record-deleted-correctly');
                return true;
            }
        } elseif ($model->loadFromCode($codes) && $model->delete()) {
            // deleting a single row
            // Tools::log()->notice('record-deleted-correctly');
            $model->clear();
            return true;
        }

        // Tools::log()->warning('record-deleted-error');
        $model->clear();
        return false;
    }

    /**
     * Return values from Widget Values for autocomplete action
     *
     * @param string $viewName
     * @param string $fieldName
     *
     * @return array
     */
    protected function getAutocompleteValues(string $viewName, string $fieldName): array
    {
        $result = [];
        $column = $this->views[$viewName]->columnForField($fieldName);
        if (!empty($column)) {
            foreach ($column->widget->values as $value) {
                $result[] = ['key' => Tools::lang()->trans($value['title']), 'value' => $value['value']];
            }
        }
        return $result;
    }

    /**
     * Return array with parameters values
     *
     * @param array $keys
     *
     * @return array
     */
    protected function requestGet(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->request->get($key);
        }
        return $result;
    }

    /**
     * Run the select action.
     * Returns a JSON string for the searched values.
     *
     * @return array
     */
    protected function selectAction(): array
    {
        $required = (bool)$this->request->get('required', false);
        $data = $this->requestGet(['field', 'fieldcode', 'fieldfilter', 'fieldtitle', 'formname', 'source', 'term']);

        $where = [];
        foreach (DataBaseWhere::applyOperation($data['fieldfilter'] ?? '') as $field => $operation) {
            $where[] = new DataBaseWhere($field, $data['term'], '=', $operation);
        }

        $results = [];
        foreach ($this->codeModel->all($data['source'], $data['fieldcode'], $data['fieldtitle'], !$required, $where) as $value) {
            $results[] = ['key' => Tools::fixHtml($value->code), 'value' => Tools::fixHtml($value->description)];
        }
        return $results;
    }
}

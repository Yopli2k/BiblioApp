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
namespace BiblioApp\Model;

use BiblioApp\Core\App\AppModel;
use BiblioApp\Core\Tools\Tools;

/**
 * Visual filter configuration of the views,
 * each PageFilter corresponds to a view or tab filter.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class PageFilter extends AppModel
{

    /**
     * Human description
     *
     * @var string
     */
    public string $description;

    /**
     * Definition of filters values
     *
     * @var array
     */
    public array $filters;

    /**
     * Primary Key.
     *
     * @var ?int
     */
    public ?int $id;

    /**
     * Name of the page (controller).
     *
     * @var string
     */
    public string $name;

    /**
     * Link to User model.
     *
     * @var ?string
     */
    public ?string $username;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->description = '';
        $this->filters = [];
        $this->id = null;
        $this->name = '';
        $this->username = null;
    }

    /**
     * Load the data from an array
     *
     * @param array $data
     * @param array $exclude
     */
    public function loadFromData(array $data = [], array $exclude = []): void
    {
        $this->description = $data['description'] ?? '';
        $this->filters = isset($data['filters']) ? json_decode($data['filters'], true) : [];
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->username = $data['username'] ?? '';
    }

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public static function tableName(): string
    {
        return 'filters';
    }

    public function test(): bool
    {
        $this->description = Tools::noHtml($this->description);
        if (empty($this->description)) {
            return false;
        }

        return parent::test();
    }

    protected function insert(): bool
    {
        $sql = 'INSERT INTO ' . static::tableName()
            . ' (description, filters, name, username)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->description) . ','
            . json_encode($this->filters) . ','
            . self::$dataBase->var2str($this->name) . ','
            . self::$dataBase->var2str($this->username)
            . ')';
        return self::$dataBase->exec($sql);
    }

    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
            . 'description = ' . self::$dataBase->var2str($this->description) . ','
            . 'name = ' . self::$dataBase->var2str($this->name) . ','
            . 'username = ' . self::$dataBase->var2str($this->username) . ','
            . 'filters = ' . json_encode($this->filters) . ','
            . ' WHERE id = ' . $this->id;
        return self::$dataBase->exec($sql);
    }
}

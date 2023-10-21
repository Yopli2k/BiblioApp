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
 * Class to manage the category data.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class Category extends AppModel
{

    /**
     * Primary Key.
     *
     * @var ?int
     */
    public ?int $id;

    /**
     * Human description.
     *
     * @var string
     */
    public string $name;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->id = null;
        $this->name = '';
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->id = (int)$data['id'] ?? null;
        $this->name = $data['name'] ?? '';
    }

    /**
     * Returns the name of the column that is the model's primary key.
     *
     * @return string
     */
    public static function primaryColumn(): string
    {
        return 'id';
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'categories';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        $this->name = Tools::noHtml(mb_strtolower($this->name ?? '', 'UTF8'));
        return parent::test();
    }

    /**
     * Returns the url where to see / modify the data.
     *
     * @param string $type
     * @param string $list
     * @return string
     */
    public function url(string $type = 'auto', string $list = 'List'): string
    {
        return parent::url($type, 'ListBook?activetab=' . $list);
    }

    /**
     * Insert the model data in the database.
     *
     * @return bool
     */
    protected function insert(): bool
    {
        $sql = 'INSERT INTO ' . static::tableName()
            . ' (name)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->name)
            . ')';
        return self::$dataBase->exec($sql);
    }

    /**
     * Returns the list of fields that are required.
     *
     * @return string[]
     */
    protected function requiredFields(): array
    {
        return ['name'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
                . 'name = ' . self::$dataBase->var2str($this->name)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }
}

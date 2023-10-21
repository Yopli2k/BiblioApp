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
 * Class to manage the member data.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class Member extends AppModel
{

    /** @var bool */
    public bool $enabled;

    /**
     * Primary Key.
     *
     * @var ?int
     */
    public ?int $id;

    /**
     * Full name of the member.
     *
     * @var string
     */
    public string $name;

    /**
     * Phone number for contact.
     *
     * @var string
     */
    public string $phone;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->enabled = true;
        $this->id = null;
        $this->name = '';
        $this->phone = '';
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->enabled = (bool)$data['enabled'] ?? false;
        $this->id = (int)$data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->phone = $data['phone'] ?? '';
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
        return 'members';
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
        $this->phone = Tools::noHtml(mb_strtolower($this->phone ?? '', 'UTF8'));
        if (false === preg_match("/^\d{10}$/", $this->phone)) {
            return false;
        }

        return parent::test();
    }

    /**
     * Insert the model data in the database.
     *
     * @return bool
     */
    protected function insert(): bool
    {
        $sql = 'INSERT INTO ' . static::tableName()
            . ' (enabled, name, phone)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->enabled) . ','
            . self::$dataBase->var2str($this->name) . ','
            . self::$dataBase->var2str($this->phone)
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
        return ['name', 'phone'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
                . 'enabled = ' . self::$dataBase->var2str($this->enabled) . ','
                . 'name = ' . self::$dataBase->var2str($this->name) . ','
                . 'phone = ' . self::$dataBase->var2str($this->phone)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }
}

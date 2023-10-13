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
 * Class to manage the categories of one book.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class BookCategory extends AppModel
{

    /**
     * Link to Book Model.
     *
     * @var ?int
     */
    public $book_id;

    /**
     * Link to Category Model.
     *
     * @var ?int
     */
    public $category_id;

    /**
     * Primary Key.
     *
     * @var ?int
     */
    public $id;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->id = null;
        $this->book_id = null;
        $this->category_id = null;
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->id = (int)$data['id'] ?? null;
        $this->book_id = (int)$data['book_id'] ?? null;
        $this->category_id = (int)$data['category_id'] ?? null;
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
        return 'books_categories';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        if (empty($this->book_id) ||empty($this->category_id)) {
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
            . ' (book_id, category_id)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->book_id) . ', '
            . self::$dataBase->var2str($this->category_id)
            . ')';
        return self::$dataBase->exec($sql);
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
                . 'book_id = ' . self::$dataBase->var2str($this->book_id) . ', '
                . 'category_id = ' . self::$dataBase->var2str($this->category_id)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }
}

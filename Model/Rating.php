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
 * Class to manage the rating of the book data.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class Rating extends AppModel
{

    /**
     * Link to the book model.
     *
     * @var int|null
     */
    public ?int $book_id;

    /**
     * Primary Key.
     *
     * @var int|null
     */
    public ?int $id;

    /**
     * Link to member model.
     *
     * @var int|null
     */
    public ?int $member_id;

    /**
     * Valoration level. From 1 to 5.
     *
     * @var int
     */
    public int $rating;

    /**
     * Date of the rating.
     *
     * @var string
     */
    public string $rating_date;

    /**
     * Time of the rating.
     *
     * @var string
     */
    public string $rating_time;

    /**
     * Valoration from the book made by member.
     *
     * @var string
     */
    public string $valoration;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->book_id = null;
        $this->id = null;
        $this->member_id = null;
        $this->rating = 5;
        $this->rating_date = date('Y-m-d');
        $this->rating_time = date('H:i:s');
        $this->valoration = '';
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->book_id = (int)$data['book_id'] ?? null;
        $this->id = (int)$data['id'] ?? null;
        $this->member_id = (int)$data['member_id'] ?? null;
        $this->rating = (int)$data['rating'] ?? 5;
        $this->rating_date = $data['rating_date'] ?? date('Y-m-d');
        $this->rating_time = $data['rating_time'] ?? date('H:i:s');
        $this->valoration = $data['valoration'] ?? '';
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
        return 'ratings';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        $this->valoration = Tools::noHtml($this->valoration);
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
            . ' (book_id, member_id, rating, rating_date, rating_time, valoration)'
            . ' VALUES ('
            . $this->book_id . ','
            . $this->member_id . ','
            . $this->rating . ','
            . self::$dataBase->var2str($this->rating_date) . ','
            . self::$dataBase->var2str($this->rating_time) . ','
            . self::$dataBase->var2str($this->valoration)
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
        return ['book_id', 'member_id', 'rating', 'valoration'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
            . 'rating = ' . $this->rating . ','
            . 'valoration = ' . self::$dataBase->var2str($this->valoration)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }
}

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
 * Class to manage the book data.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class Book extends AppModel
{

    /** @var string */
    public string $author;

    /** @var string */
    public string $editorial;

    /**
     * Primary Key.
     *
     * @var ?int
     */
    public ?int $id;

    /** @var string */
    public string $isbn;

    /** @var int */
    public int $pages;

    /** @var int */
    public int $publication;

    /** @var string */
    public string $synopsis;

    /** @var string */
    public string $title;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->author = '';
        $this->editorial = '';
        $this->id = null;
        $this->isbn = '';
        $this->pages = 0;
        $this->publication = date('Y');
        $this->synopsis = '';
        $this->title = '';
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->author = $data['author'] ?? '';
        $this->id = (int)$data['id'] ?? null;
        $this->isbn = $data['isbn'] ?? '';
        $this->synopsis = $data['synopsis'] ?? '';
        $this->title = $data['title'] ?? '';
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
        return 'books';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        $this->author = Tools::noHtml(mb_strtolower($this->author ?? '', 'UTF8'));
        $this->isbn = Tools::noHtml(mb_strtolower($this->isbn ?? '', 'UTF8'));
        $this->title = Tools::noHtml(mb_strtolower($this->title ?? '', 'UTF8'));
        $this->synopsis = Tools::noHtml(mb_strtolower($this->synopsis ?? '', 'UTF8'));

        if (false ===  is_numeric($this->isbn)) {
            $this->message->error('El ISBN debe ser un número.');
            return false;
        }

        if ($this->publication > date('Y')) {
            $this->message->error('El año de publicación no puede ser mayor que el año actual.');
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
            . ' (author, isbn, synopsis, title)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->author) . ','
            . self::$dataBase->var2str($this->isbn) . ','
            . self::$dataBase->var2str($this->synopsis) . ','
            . self::$dataBase->var2str($this->title)
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
        return ['author', 'isbn', 'title', 'synopsis', 'publication', 'pages'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
                . 'author = ' . self::$dataBase->var2str($this->author) . ','
                . 'isbn = ' . self::$dataBase->var2str($this->isbn) . ','
                . 'synopsis = ' . self::$dataBase->var2str($this->synopsis) . ','
                . 'title = ' . self::$dataBase->var2str($this->title)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }
}

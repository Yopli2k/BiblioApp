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
 * Class to manage the images of one book.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class BookImage extends AppModel
{

    const IMAGES_PATH = APP_FILES . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR;

    /**
     * Link to Book Model.
     *
     * @var int|null
     */
    public ?int $book_id;

    /**
     * The date of creation of the record.
     *
     * @var string
     */
    public string $creationdate;

    /**
     * The hour of creation of the record.
     *
     * @var string
     */
    public string $creationhour;

    /**
     * The name of the file with extension.
     *
     * @var string
     */
    public string $filename;

    /**
     * The path of the file.
     *
     * @var string
     */
    public string $filepath;

    /**
     * The mimetype of the file.
     *
     * @var string
     */
    public string $filetype;

    /**
     * The size of the file in bytes.
     *
     * @var int
     */
    public int $filesize;

    /**
     * Primary Key.
     *
     * @var int|null
     */
    public ?int $id;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->book_id = null;
        $this->creationdate = date('d-m-Y');
        $this->creationhour = date('H:i:s');
        $this->filename = '';
        $this->filepath = '';
        $this->filetype = '';
        $this->filesize = 0;
        $this->id = null;
    }

    /**
     * Remove the model data from the database.
     * If the record is deleted, the file is also deleted.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if (false === parent::delete()) {
            return false;
        }

        $file = empty($this->filepath)
            ? self::IMAGES_PATH . $this->filename
            : $this->filepath . $this->filename;
        unlink($file);
        return true;
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
        $this->creationdate = $data['creationdate'] ?? date('d-m-Y');
        $this->creationhour = $data['creationhour'] ?? date('H:i:s');
        $this->filename = $data['filename'] ?? '';
        $this->filepath = $data['filepath'] ?? '';
        $this->filetype = $data['filetype'] ?? '';
        $this->filesize = (int)$data['filesize'] ?? 0;
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
        return 'books_images';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        $this->filename = Tools::noHtml($this->filename ?? '');
        $this->filepath = Tools::noHtml($this->filepath ?? '');
        $this->filetype = Tools::noHtml($this->filetype ?? '');
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
            . ' (book_id, creationdate, creationhour, filename, filepath, filetype, filesize)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->book_id) . ', '
            . self::$dataBase->var2str($this->creationdate) . ', '
            . self::$dataBase->var2str($this->creationhour) . ', '
            . self::$dataBase->var2str($this->filename) . ', '
            . self::$dataBase->var2str($this->filepath) . ', '
            . self::$dataBase->var2str($this->filetype) . ', '
            . $this->filesize
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
        return ['book_id', 'filename', 'filepath', 'filetype'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        return true;
    }
}

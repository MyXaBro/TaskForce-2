<?php

namespace taskforce\converter;

use app\src\exception\ConverterException;
use DirectoryIterator;
use SplFileInfo;
use SplFileObject;

class CsvSqlConverter
{
    /**
     * @var SplFileInfo[]
     */
    protected array $filesToConvert = [];

    /**
     * CsvSqlConverter constructor
     * @param string $directory
     * @throws ConverterException
     */
    public function __construct(string $directory)//указываем директорию где лежат csv файлы
    {
        if (!is_dir($directory)) {
            throw new ConverterException('Указанная директория не существует');
        }

        $this->loadCsvFiles($directory);
    }

    /**
     * Метод для конвертации файлов, который возвращает массив
     * @param string $outputDirectory
     * @return array
     */
    public function ConvertFiles(string $outputDirectory):array
    {
        $result = [];

        foreach($this->filesToConvert as $file){
            $result[] = $this->convertFile($file, $outputDirectory);
        }

        return $result;
    }

    /**
     * Функция конвертирует файл и возвращает строку
     * @param SplFileInfo $file
     * @param string $outputDirectory
     * @return string
     */
    protected function ConvertFile(SplFileInfo $file, string $outputDirectory): string
    {
        $fileObject = new SplFileObject($file->getRealPath());
        $fileObject->setFlags(SplFileObject::READ_CSV);

        $columns = $fileObject->fgetcsv();
        $values = [];

        while(!$fileObject->eof()){
            $values[] = $fileObject->fgetcsv();//получаем из каждой строки данные в виде массива
        }

        $tableName = $file->getBasename('.csv');
        $sqlContent = $this->getSqlContent($tableName, $columns, $values);//передаём данные в метод для записи в бд

        return $this->saveSqlContent($tableName, $outputDirectory, $sqlContent);
    }

    /**
     * Функция для вставки значений в таблицу sql
     * @param string $tableName
     * @param array $columns
     * @param array $values
     * @return string
     */
    protected function getSqlContent(string $tableName, array $columns, array $values):string
    {
        $columnsString = implode(', ', $columns);
        $sql = "INSERT INTO $tableName ($columnsString) VALUES";

        foreach ($values as $row){
            array_walk($row, function (&$value){
               $value = addslashes($value);
               $value = "'$value'";
            });

            $sql .= "( " . implode(', ', $row) . "), ";
        }

        $sql = substr($sql, 0, -2);

        return $sql;
    }

    /**
     * Функция сохраняет данные в новый файл
     * @param string $tableName
     * @param string $directory
     * @param string $content
     * @return string
     * @throws ConverterException
     */
    protected function saveSqlContent(string $tableName, string $directory, string $content):string
    {
        if(!is_dir($directory)){
            throw new ConverterException('Директория для выходных данных не существует');
        }

        $filename = $directory . DIRECTORY_SEPARATOR . $tableName . ' .sql';
        file_put_contents($filename, $content);

        return $filename;
    }

    /**
     * Функция загружает csv в массив
     * @param string $directory
     * @return void
     */
    protected function LoadCsvFiles(string $directory):void
    {
        foreach (new DirectoryIterator($directory) as $file){
            if($file->getExtension() == 'csv'){
                $this->filesToConvert[] = $file->getFileInfo();
            }
        }
    }
}

<?php
// Класс-контекст для импорта
/*
 Концепция: При вызове экшиона "actionImport" вначале создаём об'ект контекста TasuImportContext.
 При вызове конструктора подаём ему название таблицы, названия полей таблицы и csv-файлы
 
 Затем, для каждой строки csv-файла вызывается специальная функция этого класса, которая запоминает значения из строки
 csv-файла. После вызова данного метода - вызываем специальный функции, которые сами внутри себя конструируют запросы
 для прочитывания и записывания информации в базу данных
 */
class TasuImportContext {
    
    private $fields = null;
    private $table = null;
    private $key = null;
    
    
    private $sql;
    private $sqlWhere = ''; // Строка для предложения Where
    private $sqlInsert;
    private $sqlInsertFields = '(';
    private $sqlInsertPlaceholders = 'VALUES(';
    private $data = array();
    private $updateFieldValues = array();
    private $sqlCopy;
    private $sqlInsertPlaceholdersCopy;
    
    public function __construct($inFields,$inTable,$inKey)
    {
        $this->fields = $inFields;
        $this->table = $inTable;
        $this->key = $inKey;
        
        $this->sql = 'SELECT * FROM mis.'.$table.' t WHERE ';
        $this->sqlInsert = 'INSERT INTO mis.'.$table.' ';
        
        
        foreach($this->fields as $obj) {
            $this->sqlWhere .= 't.'.$obj['dbField'].' = :'.$obj['dbField'].' AND ';
            $this->data[':'.$obj['dbField']] = $obj['tasuField']; // Пока нет данных, то ставим соответствие номер поля в CSV
            $this->sqlInsertFields .= $obj['dbField'].',';
            $this->sqlInsertPlaceholders .= ':'.$obj['dbField'].',';
            $this->updateFieldValues[$obj['dbField']] = '';
        }
        
        $this->sqlWhere = mb_substr($sqlWhere, 0, mb_strlen($sqlWhere) - 5);
        $this->sqlInsertFields = mb_substr($sqlInsertFields, 0, mb_strlen($sqlInsertFields) - 1);
        $this->sqlInsertFields .= ')';
        $this->sqlInsertPlaceholders = mb_substr($sqlInsertPlaceholders, 0, mb_strlen($sqlInsertPlaceholders) - 1);
        $this->sqlInsertPlaceholders .= ')';
    }
    
    public function onBeforeRowTreating($inCurrentRow)
    {
        $csvArr = explode(',', $inCurrentRow);
        $tempArr = $this->data;
        $this->sqlCopy = $this->sql.$this->sqlWhere;
        $this->sqlInsertPlaceholdersCopy = $this->sqlInsertPlaceholders;
        foreach($this->data as $key => &$field) {
            $field = $csvArr[$field];
            // Заменяем в запросе
            $this->sqlCopy = str_replace($key, "'".str_replace("'","''",mb_convert_encoding($field, "UTF-8"))."'", $this->sqlCopy);
            $this->sqlInsertPlaceholdersCopy  = str_replace($key, "'".str_replace("'","''",mb_convert_encoding($field, "UTF-8"))."'", $this->sqlInsertPlaceholdersCopy);
            //$this->sqlCopy = str_replace($key, "'".str_replace("'","''",$field)."'", $this->sqlCopy);
            //$this->sqlInsertPlaceholdersCopy  = str_replace($key, "'".str_replace("'","''",$field)."'", $this->sqlInsertPlaceholdersCopy);
        }
    }
    
    // Возвращает sql-запрос для нахождения копии обрабатываемой строки
    public function getSqlCopy()
    {
        return $this->sqlCopy;
    }
    
    // Возвращает запрос для добавления текущей строки в базу
    public function getInsertSql()
    {
        return  $this->sqlInsert.' '.$this->sqlInsertFields.' '.$this->sqlInsertPlaceholdersCopy;
    }
    
    
    /*
    public function Test()
    {
        echo ('!!!');
        exit();
    }
    */
}
?>

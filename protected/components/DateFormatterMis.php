<?php
class DateFormatterMis extends CComponent {

    /*
        Кастомный форматтер даты для МИС.
        пример использования:
        -------------------
        $DFM = new DateFormatterMis('1990-02-12');
        var_dump($DFM->getFullAge());

        Результат:
        string(40) "24 года 7 месяцев 28 дней"
        ------------------

    */
    private $_dateToFormat = null;

    // Конструктор принимает дату в строковом представлении
    public function __construct($dateToConvert) {
        $this->_dateToFormat = strtotime($dateToConvert);
    }

    // Функция вычисляет месяц, год, и день от даты, заданной в конструкторе
    public function getFullAge()
    {
        // Сегодняшняя дата
        $sec_now = time();
        $sec_birthday = $this->_dateToFormat;
        // Подсчитываем количество месяцев, лет
        for($time = $sec_birthday, $month = 0;
            $time < $sec_now;
            $time = $time + date('t', $time) * 86400, $month++){
            $rtime = $time;
        }
        $month = $month - 1;
        // Количество лет
        $year = intval($month / 12);
        // Количество месяцев
        $month = $month % 12;
        // Количество дней
        $day = intval(($sec_now - $rtime) / 86400);
        $result = $this->declination($year, "год", "года", "лет")." ";
        $result .= $this->declination($month, "месяц", "месяца", "месяцев")." ";
        $result .= $this->declination($day, "день", "дня", "дней")." ";
        return trim($result);
    }

    // Функция возвращает просклоненные по падежам словам в зависимости от числительных
    /*  1 месЯЦ, 2 месяЦА и пр */
    private function declination($num, $one, $ed, $mn, $notnumber = false)
    {
        // $one="статья";
        // $ed="статьи";
        // $mn="статей";
        if($num === "") print "";
        if(($num == "0") or (($num >= "5") and ($num <= "20")) or preg_match("|[056789]$|",$num))
            if(!$notnumber)
                return "$num $mn";
            else
                return $mn;
        if(preg_match("|[1]$|",$num))
            if(!$notnumber)
                return "$num $one";
            else
                return $one;
        if(preg_match("|[234]$|",$num))
            if(!$notnumber)
                return "$num $ed";
            else
                return $ed;
    }



}
?>
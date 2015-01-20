<?

/**
 * @var $this LController
 */

$this->widget("LModal", [
    "title" => "Тестовое модальное окно",
    "id" => "testModalWindow",
    "body" => $this->widget("LForm", [
        "title" => "Hello, World"
    ], true),
    "buttons" => [[
        "class" => "btn btn-primary",
        "text" => "Действие"
    ]]
]);
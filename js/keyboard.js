$(document).ready(function(e) {
    (function(config) {
        var currentNode = null;
        var parentNode = null;
        var link = '.keyboard-help-link';

        $(link).popover({
            placement: 'bottom',
            html: true,
            content: function() {
                return $(".pop-keyboard-help").html();
            }
        });

        // Нет текущего узла - значит, это корневой.
        if(currentNode == null) {
            currentNode = config.nodes;
            // Ставим упоминание о мастер-клавише
            var rootKeyElement = $('<div>').addClass('masterkey');
            $(rootKeyElement).text(config.masterkeyDesc);
            $(".pop-keyboard-help").append(rootKeyElement);
            // Ставим упоминание о up-level-клавише
            var upLevelKeyElement = $('<div>').addClass('uplevelkey');
            $(upLevelKeyElement).text(config.upLevelKeyDesc);
            $(".pop-keyboard-help").append(upLevelKeyElement);
        }
        // Стираем всё перед тем, как переназначить клавиши
        (function createTipContent(nodes) {
            $(".pop-keyboard-help .nodekey").remove();
            for(var i = 0; i < nodes.length; i++) {
                (function(node) {
                    var nodeElement = $('<div>').addClass('nodekey');
                    $(nodeElement).text(node.keyDesc);
                    $(".pop-keyboard-help").append(nodeElement);
                    // Подвязываем обработчики к формам
                    $(document).on('keydown', function(e) {
                        if(e.keyCode != node.key && e.keyCode != config.masterkey && e.keyCode != config.upLevelKey) {
                            return false;
                        }
                        $(currentNode.node).trigger('blur');
                        // Для некорневого узла убираем подсветку при переходе
                        if(currentNode.hasOwnProperty('node')) {
                            $(currentNode.node).removeClass('background-keyboard-plugin');
                        }

                        if(e.keyCode == config.masterkey) { // Мастер-клавиша
                            currentNode = config.nodes;
                            parentNode = null;
                        }
                        if(e.keyCode == config.upLevelKey) { // Клавиша уровня выше
                            if(parentNode != null) {
                                currentNode = parentNode; // Возвращаемся на родительский узел
                            }
                        }
                        // Это для некорневого узла
                        if(e.keyCode == node.key) {
                            $(node.node)
                                .focus()
                                .addClass('background-keyboard-plugin');
                            node.handler();
                            parentNode = currentNode; // Сохраняем родительский узел
                            currentNode = node;
                        }

                        // Открепляем обработчики
                        $(document).off('keydown');
                        $(link).popover('hide');
                        if(currentNode.hasOwnProperty('children')) {
                            createTipContent(currentNode.children);
                        } else {
                            console.log(currentNode);
                            createTipContent(currentNode); // Корневой узел
                        }
                        $(link).popover('show');
                    });
                })(nodes[i]);
            }
        })(config.nodes);

        $(link).on('click', function(e) {
            $(this).popover('show');
        }).on('blur', function() {
            //$(this).popover('hide');
        });
    })({
        'masterkeyDesc' : 'ESC - выход в главное меню', // Описание мастер-клавиши
        'masterkey' : 27, // Код мастер-клавиши
        'upLevelKeyDesc' : 'Стрелка вверх - вернуться на уровень выше', // Описание клавиши "уровень выше"
        'upLevelKey' : 38, // Код клавиши для уровня выше
        'nodes' : [
            {
                'node' : $('#mainSideMenu'), // Здесь селектор для ноды
                'key' : 37, // Здесь код для клавиши
                'keyDesc' : 'Стрелка влево - переход в меню', // Здесь описание клавиши в подсказке
                'handler' : function() { // Хандлер, исполняющийся при переходе на элемент
                    //alert(1);
                },
                'children' :
                    [
                        // Это узлы-потомки
                        {
                            'node' : $('#mainSideMenu'),
                            'key' : 47,
                            'keyDesc' : '0 - какое-то действие',
                            'handler' : function() {
                                //alert(3);
                            },
                            'children' :
                                [

                                ]
                        }
                    ]
            },
            {
                'node' : $('#patient-search-form'),
                'key' : 39,
                'keyDesc' : 'Стрелка вправо - переход в форму поиска пациента',
                'handler' : function() {
                    //alert(2);
                },
                'children' :
                [

                ]
            }
        ]
    });
});
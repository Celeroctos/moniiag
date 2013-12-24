$(document).ready(function(e) {
    
    // Стандартная конфигурация для всех страниц
        StandartConfig = {
        'masterkeyDesc' : 'ESC - выход в главное меню', // Описание мастер-клавиши
        'masterkey' : 27, // Код мастер-клавиши
        'upLevelKeyDesc' : 'Стрелка вверх - вернуться на уровень выше', // Описание клавиши "уровень выше"
        'upLevelKey' : 38, // Код клавиши для уровня выше
        'nodes' : [
           
        ]
    };

    //   1.Надо определить по URL странице какую конфигурацию прочитать
    //   2.Надо совместить стандартную конфигурацию корневого элемента с конфигурацией корневого элемента
    //     (Записать в nodes корневого элемента индивидуальную конфигурацию для страницы)
    //   3.Для собранной конфигурации подвязываем обработчики
    
    //alert('http://mis.my/index.php/reception/patient/viewsearch'.match('\.php/reception/patient/viewsearch'))
    
    // Берём href
    var pageAddr = window.location.href;
    var UserConfiguration=null;
    
    // Перебираем конфигурации различный страниц
    for (i=0;i<keyboard_cnf.page_configs.length;i++) {
        // Если href текущей страницы матчится на конфигурацию 
        //   по номеру i - мы нашли конфигурацию для данной страницы
        if (pageAddr.match(keyboard_cnf.page_configs[i].page_key)!=null) {
                // Сохраняем найденную конфигурацию для страницы
               UserConfiguration = keyboard_cnf.page_configs[i].page_config
               // Больше не хочу выполнять этот цикл (конфигурация же найдена)
               break;
        }
        
    }
    
    if (UserConfiguration!=null) {
        // Если нашли конфигурацию, то прихреначиваем её к стандартной
        StandartConfig.nodes = UserConfiguration;
    }
    // Вызываем привязку 
    InitKeybordNavigation(StandartConfig)
    
    //alert("");
    
    function InitKeybordNavigation(config) {
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
                        
                        console.log("node.key="+node.key,"e.key="+e.key);
                        
                        // Это для некорневого узла
                        if(e.keyCode == node.key) {
                            $(node.node)
                                .focus()
                                .addClass('background-keyboard-plugin');
                            node.handler();
                            parentNode = currentNode; // Сохраняем родительский узел
                            currentNode = node;
                        }

                        
                        //$(link).popover('hide');
                        if(currentNode.hasOwnProperty('children')) {
                            // Если детей нет, то не снимаем обработчик.
                            if (currentNode.children.length!=0) {
                                //createTipContent(config);
                                
                                // Открепляем обработчики
                                $(document).off('keydown');
                                createTipContent(currentNode.children);
                            }
                        } else {
                            console.log(currentNode);
                            // Открепляем обработчики
                            $(document).off('keydown');
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
    }
});
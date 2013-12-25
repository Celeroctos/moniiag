

$(document).ready(function(e) {
    
    // Конфигурация только для левого меню
    MenuNode =
    {
        'node' : '#mainSideMenu',
        'id': 'side-menu',
        'handler' : function()
        { // Хандлер, исполняющийся при переходе на элемент
                            //alert(1);
        },
        
        
    };

    //   1.Надо определить по URL странице какую конфигурацию прочитать
    //   2.Прииготовить эту конфигурацию (создать индекс узлов, чтобы к ним можно было обращаться по id),
    //         подвязать переход в меню и из него
    //   3.Для подготовленной конфигурации подвязываем обработчики
    
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
               UserConfiguration = keyboard_cnf.page_configs[i];
               // Больше не хочу выполнять этот цикл (конфигурация же найдена)
               break;
        }
        
    }
    
    // Если нашли конфигурацию, то делаем привязку
    //  InitKeybordNavigation(UserConfiguration)
    if (UserConfiguration!=null) {
        
        // Создаём объект индекса для доступа к узлам по их id
        PrepareConfiguration(UserConfiguration);
        // Вызываем привязку
        InitKeybordNavigation(UserConfiguration);

    }
    

    
    //alert("");
    
    // Функция готовит проитанную конфигурацию для использования
    function PrepareConfiguration(Config) {
        
         // Привязываем ещё один узел для перехода в меню
         
         // Создадим переходы из меню на первый элемент конфигурации (по клавише "стрелка вправо")
        WaysToFirst = new Array();
        WaysToFirstElement = {};
        WaysToFirstElement.target=Config.nodes[0].id;
        WaysToFirstElement.key=39;
        WaysToFirst.push(WaysToFirstElement);
        
        // Заполняем ссылку на дуги у элемента для меню
        MenuNode.ways = WaysToFirst;
        
        // Создадим переход из любого блока в пункт меню
        WayToMenu = {};
        WayToMenu.target = MenuNode.id;
        WayToMenu.key = 37;
        

        
        // Дописываем в каждый узел, из которого нет дуги по ссылке влево - путь в узел, соответствующей узлу меню,
        //  чтобы из любого узла, который справа от меню можно было попасть в меню
        //   Перебираем узлы
        for (i=0;i<Config.nodes.length;i++) {
            // Флаг о том, что в узле есть дуга по стрелке влево
            IsArrowLeft = false;
            
            // Перебираем дуги узла
            for (j=0;j<Config.nodes[i].ways.length;j++) {
                
                // Если дуга по кнопке "стрелка влево", то значит она уже занята в данном узле - выходим из цикла
                if (Config.nodes[i].ways[j]==37) {
                    IsArrowLeft = true;
                    break;
                }
            
            }
            
            // Есдли нет дуги по стрелке влево
            if (!IsArrowLeft) {
                Config.nodes[i].ways.push (WayToMenu);
                
            }
        }
        
        // Добавляем в список узлов элемент, соответствующий главному меню
        Config.nodes.push(MenuNode);
        
        // Создаём объект индекса для доступа к узлам по их id
        CreateIndex(Config) 
    }
    
    function CreateIndex(Config)
    {
        index = new Array();
        for (i=0;i<Config.nodes.length;i++) {
            index[Config.nodes[i].id]=i;   
        }
        Config.index = index;
    }
    
    function InitKeybordNavigation(config) {
        var currentNode = null;
        var PreviousNode = null;
        var MasterKeyWasPressed = false;
        //var link = '.keyboard-help-link';

        /*
        $(link).popover({
            placement: 'bottom',
            html: true,
            content: function() {
                return $(".pop-keyboard-help").html();
            }
        });
        */
        
        // Ставим обработчик, цель которого - проверить, если была отпущена клавиша ESC,
        //  то надо сбросить соответствующий флаг
        $(document).on('keyup', function(e) {
            if (e.keyCode==27) {
                MasterKeyWasPressed = false;
            }
        });

        // Нет текущего узла - значит, его надо проинициализировать.
        if(currentNode == null) {
            currentNode = config.nodes[0];
            
        $(currentNode.node).addClass('background-keyboard-plugin');
            // Ставим упоминание о мастер-клавише
           /* var rootKeyElement = $('<div>').addClass('masterkey');
            $(rootKeyElement).text(config.masterkeyDesc);
            $(".pop-keyboard-help").append(rootKeyElement);
            // Ставим упоминание о up-level-клавише
            var upLevelKeyElement = $('<div>').addClass('uplevelkey');
            $(upLevelKeyElement).text(config.upLevelKeyDesc);
            $(".pop-keyboard-help").append(upLevelKeyElement);*/
        }
        // Стираем всё перед тем, как переназначить клавиши
        (function createTipContent(ways) {
            //$(".pop-keyboard-help .nodekey").remove();
            // Для текущего узла перебираем все дуги, выходящие из него
            for(var i = 0; i < ways.length; i++) {
                (function(way) {
                    //var nodeElement = $('<div>').addClass('nodekey');
                    //$(nodeElement).text(node.keyDesc);
                    //$(".pop-keyboard-help").append(nodeElement);
                    // Подвязываем обработчики к формам
                    $(document).on('keydown', function(e) {
                        
                        // Если нажата Esc - ставим флажок
                         if(e.keyCode == 27 ) {
                            MasterKeyWasPressed = true;
                            return;
                        }
                        
                        // Если не нажат Esc, то дальше вообще не стоит ходить
                        if (!MasterKeyWasPressed) {
                            return;
                        }
                        
                        
                        // Если клавиша не совпадает с путём - ничего не делаем
                        if(e.keyCode != way.key) {
                            return false;
                        }
                        $(currentNode.node).trigger('blur');
                        // Для некорневого узла убираем подсветку при переходе
                           $(currentNode.node).removeClass('background-keyboard-plugin');
                        
                        // Отрабатываем handler дуги
                        if (way.hasOwnProperty('handler')) {
                            way.handler();
                        }
                        
                        
                        // Берём узел, на который ссылается указатель в ребёнке, в котором произошло событие
                        // Проверяем - есть ли у дуги target, переходим по нему
                        if (way.hasOwnProperty('target')) {
                            
                            // Определяем узел для перехода
                            NextNode = config.nodes[config.index[way.target]];
                            
                            // Если текущих узел - это меню и клавиша - стрелка вправо, то следующий узел надо взять
                            //   не по target, а Previous
                            if ((currentNode.id==MenuNode.id) &&(e.keyCode==39)){
                                NextNode = PreviousNode;
                            }

                            // Выполняем переход по этому узлу
                          $(NextNode.node)
                                .focus()
                                .addClass('background-keyboard-plugin');
                            if (NextNode.hasOwnProperty('handler')) {
                                NextNode.handler();
                            }
                            // Записываем последний выделенный узел
                            PreviousNode = currentNode;
                            currentNode = NextNode;
                        
                            if (currentNode.hasOwnProperty('handler')) {
                                currentNode.handler();
                            }
                        }

                            // Если из нового узла можно куда-то перейти, то берём его дуги
                            //   и рекурсивно вызываем функцию инициализации событий нажатия клавиши для дуг
                            if (currentNode.hasOwnProperty('ways')) {
                                if (currentNode.ways.length>0)
                                {
                                    // Выключаем старые события
                                    $(document).off('keydown');
                                    
                                    createTipContent(currentNode.ways);    
                                }
                            }

                        //$(link).popover('show');
                    });
                })(ways[i]);
            }
        })(config.nodes[0].ways);

       /* $(link).on('click', function(e) {
            $(this).popover('show');
        }).on('blur', function() {
            //$(this).popover('hide');
        });*/
    }
});

//======================================================================================
//======================================================================================
//  Ниже старое и скорее всего ненужное
//======================================================================================
//======================================================================================

/*
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
*/
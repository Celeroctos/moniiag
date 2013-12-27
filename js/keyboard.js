

$(document).ready(function(e) {
    
    //var test = $('#timePerPatient1').focus();
    //alert(test);
    
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
        //var Test = $('#ui-pg-input');
        //Test.focus();
    }
    
    // Функция готовит проитанную конфигурацию для использования
    function PrepareConfiguration(Config) {
        
         // Привязываем ещё один узел для перехода в меню
         
         // Создадим переходы из меню на первый элемент конфигурации (по клавише "стрелка вправо")
        WaysToFirst = new Array();
        WaysToFirstElement = {};
        WaysToFirstElement.target=Config.nodes[0].id;
        WaysToFirstElement.key=39;
        WaysToFirstElement.keyDesc = 'Стрелка вправо - выйти из меню';
        WaysToFirst.push(WaysToFirstElement);
        
        // Заполняем ссылку на дуги у элемента для меню
        MenuNode.ways = WaysToFirst;
        
        // Создадим переход из любого блока в пункт меню
        WayToMenu = {};
        WayToMenu.target = MenuNode.id;
        WayToMenu.key = 37;
        WayToMenu.keyDesc = 'Стрелка влево - перейти в меню';
        
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
    
    // Ставит на элемент фокус и, если он установился, возвращает true
    //   иначе возвращает false
    function TrySetFocus(ElementToFocus)
    {
        // Ставим на элемент фокус
        $(ElementToFocus).focus();
        
        // Проверяем, установился ли он
        if (!$(ElementToFocus).is(':focus')) {
            return false;
        }
        return true;
    }
    
    
    function InitKeybordNavigation(config) {
        var MasterKeyCode = 27; // Код мастер-клавиши
        var NodeKeysElements = $('<div>'); // Накопитель для сообщений в поп-апе помощи по клавиатуре
        var currentNode = null;// Текущий активный узел графа
        var PreviousNode = null;// Предыдущий активный узел графа
        var MasterKeyWasPressed = false;// Флаг о том, что нажата мастер-клавиша
        var WasPopupRefreshed = false;// Флаг о том, что был ли обновлён поп-ап. Если поменялось состояние системы,
        //    то нужно поп-ап обновить. Этот флаг говорит о том, что для этого состояния поп-ап был обновлён
        var WasPopupOpened = false;// Флаг о том, был ли открыт поп-ап
               
        
        // Ссылка "Помощь по клавиатуре"
        var link = '.keyboard-help-link';
        $(link).popover({
            placement: 'bottom',
            html: true,
            content: function() {
                return $(".pop-keyboard-help").html();
            }
        });
        // Ставим обработчик, цель которого - проверить, если была отпущена мастер-клавиша,
        //  то надо сбросить соответствующий флаг и удалить сообщения с подсказками из
        //    поп-апа обновить этот поп-ап
        $(document).on('keyup', function(e) {
            console.log("?");
            if (e.keyCode==MasterKeyCode) {
                console.log("!");
                // Мастер-клавиша отпущена
                MasterKeyWasPressed = false;
                // Поп-ап не нужно обновлять
                WasPopupRefreshed = false;
                // Очищаем сообщение о клавишах
                            // Удаляем сообщения из поп-апа
                            $(".pop-keyboard-help .nodekey").remove();
                            // Если поп-ап был открыт, то обновляем его
                            if (WasPopupOpened) {
                                $(link).popover('show'); 
                            }
            }
        });
        // Нет текущего узла - значит, его надо проинициализировать.
       // if(currentNode == null) {
            currentNode = config.nodes[0];
            
        // Ставим фокус на первый элемент первого блока
        // Выберем все элементы для блока
        var TabElements = ChooseTabElementsInContainer($(currentNode.node)[0]);
        
        // Пробегаемся по элементам массива TabElements снизу, находим первый
        //     элемент, у которого табиндекс ненулёвый ставим фокус на него, затем вызываем break
        // Ставим фокус на первый
        for (i=0;i<TabElements.length;i++)
        {
            if (TrySetFocus(TabElements[i]))
            {
                break;
            }
        }
        
        
        //(TabElements[0]).focus();
            
            
            
       // Подвешиваем на все инпуты и селекты в форме следующий обработчик:
    //      По нажатию на таб проверяем - является ли текущий инпут последним в форме
    //         если является, то необходимо перебросить фокус на
    //           первый инпут в этой же форме. Таким образом табуляция будет "закольцована"
    //           по полям формы
    

        // Выбираем всё что можно выделить табом на странице
        //var Controls = ChooseTabElementsInContainer($('html'));
        
        // Перебираем всё, что выделили
        //for (i=0;i<Controls.length;i++) {
            // Подвязываем обработчик
            $('html').on('keydown', 'a, input, select, button, textarea',function (e)  {
                console.log('keydown fires');
                PressTabControlHandler(e);
            });
        //}

    // Обрабатывает нажатие таб на контроле. Сделано для того, чтобы после нажатия таба
    //    на последнем контроле в форме был переход фокуса
    //   на первый контрол той же формы
    function PressTabControlHandler(Target) {
        if (Target.keyCode==9) {
            
            // Получаем для текущего активного узла элементы, по которым можно переходить по табу
            var TabElements = ChooseTabElementsInContainer($(currentNode.node)[0]);
            
            // Находим первый элемент с конца, у которого табиндекс больший
            var LastTabIndex = TabElements.length-1;
            while (LastTabIndex>=0)
            {
                if (TabElements[LastTabIndex].tabIndex>=0 || !TabElements[LastTabIndex].hasOwnProperty('tabIndex') ) {
                    break;
                }
                LastTabIndex--;
            }
            
            // Проверим - является ли элемент, на котором случилось событие последним для текущего контейнера
            if (TabElements[LastTabIndex]==Target.currentTarget) {
                // Ставим фокус первом у элементу из TabElements
                //(TabElements[0]).focus();
                for (i=0;i<TabElements.length;i++)
                {
                    if (TrySetFocus(TabElements[i])) {
                        break;
                    }
                }
            
                Target.preventDefault();
            }

        }
    }  
            
            
            
            
        $(currentNode.node).addClass('background-keyboard-plugin');
            // Ставим упоминание о мастер-клавише
            var rootKeyElement = $('<div>').addClass('masterkey');
            $(rootKeyElement).text('Нажмите и держите клавишу ESC для перемещения');
            $(".pop-keyboard-help").append(rootKeyElement);

        
        // Выбирает все элементы в контейнере, на которые можно перейти табулятором
        function ChooseTabElementsInContainer(Container)
        {
            var Result = 
            //$(Container).find("a, input, select, button, textarea");
            $(Container).find("a, input, select, button, textarea");
            return Result;
        }
        
        // Обновить поп-ап "Помощь по клавиатуре". Вызывается, тогда, когда есть вероятность, что
        //   поменялся узел или нажата мастер-клавиша
        function RefreshPopup()
        {
            // Очистить попап от старых сообщений
                            $(".pop-keyboard-help .nodekey").remove();
                            if (WasPopupOpened && (!WasPopupRefreshed)) {
                                $(link).popover('show'); 
                            }
                            // Берём новые сообщения и записываем их в поп-ап
                            $(".pop-keyboard-help").append($(NodeKeysElements).html());
                            // Если поп-ап был открыт и не был обновлён - выводим его
                            if (WasPopupOpened && (!WasPopupRefreshed)) {
                                WasPopupRefreshed = true;
                                $(link).popover('show'); 
                            }
        }
        // Стираем всё перед тем, как переназначить клавиши
        (function createTipContent(ways) {
            $(".pop-keyboard-help .nodekey").remove();
            NodeKeysElements =  $('<div>');
            // Для текущего узла перебираем все дуги, выходящие из него
            for(var i = 0; i < ways.length; i++) {
                (function(way) {
                    // Выводим описание пути в подсказку
                    // Создаём новое сообщение
                    var nodeElement = $('<div>').addClass('nodekey');
                    // Записываем в него текст
                    $(nodeElement).text(way.keyDesc);
                    // Добавляем сообщение в список сообщений
                    $(NodeKeysElements).append(nodeElement);
                    // Подвязываем обработчики к формам
                    $(document).on('keydown', function(e) {
                        // Если нажата мастер-клавиша - ставим флажок и обновляем поп-ап
                         if(e.keyCode == MasterKeyCode ) {
                            MasterKeyWasPressed = true;
                            RefreshPopup()
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
                        // Отрабатываем handler дуги
                        if (way.hasOwnProperty('handler')) {
                            way.handler();
                        }
                        // Берём узел, на который ссылается указатель в ребёнке, в котором произошло событие
                        // Проверяем - есть ли у дуги target, переходим по нему
                        if (way.hasOwnProperty('target')) {
                            
                           $(currentNode.node).trigger('blur');
                            // Для некорневого узла убираем подсветку при переходе
                           $(currentNode.node).removeClass('background-keyboard-plugin');
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
                            
                            $(':focus').blur();
                            
                            // Ставим фокус на первый элемент блока
                            var TabElements = ChooseTabElementsInContainer($(currentNode.node)[0]);
                            for (i=0;i<TabElements.length;i++)
                            {
                                if (TrySetFocus(TabElements[i])) {
                                    break;
                                }
                            }
                            
                            
                            // Если у текущего узла есть хендлер - вызываем его
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
                                    WasPopupRefreshed = false;
                                }
                            }
                        if (WasPopupOpened) {
                            RefreshPopup();
                        }
                    });
                })(ways[i]);
            }
        })(config.nodes[0].ways);
        $(link).on('click', function(e) {
            WasPopupOpened = true;
            $(this).popover('show');
        }).on('blur', function() {
            WasPopupOpened = false;
            $(this).popover('hide');
        });
    }
});

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
    
    //alert('http://mis.my/reception/patient/viewsearch'.match('\.php/reception/patient/viewsearch'))
    
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
        
        // Выбираем все элементы,у которых tabIndex отрицательный или undefinded
        Config.wasInitedNotTabbedElements = false;
        
        // Читаем элементы, у которых отрицательный таб-индекс
        var AllElements = ChooseTabElementsInContainer($('html'));
        var NegativeTabIndex = new Array();
        
        for (i=0;i<AllElements.length;i++ )
        {
            //if ($(AllElements[i]).tabIndex==undefined || $(AllElements[i]).tabIndex<0) {
            if (($(AllElements[i])[0]).tabIndex<0) {
            
                NegativeTabIndex.push(AllElements[i]);
            }
        }
        
        Config.negativeTabIndex = NegativeTabIndex;
        
        /*
        var AllElements = ChooseTabElementsInContainer($('html'));
        var ElementsNotTabbedBeginner = new Array();
        
        for (i=0;i<AllElements.length;i++ )
        {
            //if ($(AllElements[i]).tabIndex==undefined || $(AllElements[i]).tabIndex<0) {
            if ($(AllElements[i]).tabIndex<0) {
            
                ElementsNotTabbedBeginner.push(AllElements[i]);
            }
        }
        
        Config.NotTabbed = ElementsNotTabbedBeginner;
        */
    }
    
    function CreateTabArrays (Config)
    {
                // Перебираем узлы и для каждого берём массив элементов, у которых табиндекс больше нуля или неопределён и записываем
        //   в объекты узлов
         for (i=0;i<Config.nodes.length;i++) {
            var Controls = ChooseTabElementsInContainer($(Config.nodes[i].node));
            var ControlsTabbed = new Array();
            for (j=0;j<Controls.length;j++)
            {
                //if ($(Controls[j]).tabIndex==undefined || $(Controls[j]).tabIndex>=0) {
                //    ControlsTabbed.push(Controls[j]);
                //}
                
                if (TrySetFocus($(Controls[j]))) {
                    ControlsTabbed.push(Controls[j]);
                }
                
            }
            Config.nodes[i].TabbedElements = ControlsTabbed;
         }
        
        // Тоже смое делаем для поп-апов
                 for (i=0;i<Config.popups_ids.length;i++) {
            var Controls = ChooseTabElementsInContainer($('#'+Config.popups_ids[i].id));
            var ControlsTabbed = new Array();
            for (j=0;j<Controls.length;j++)
            {
                //if ($(Controls[j]).tabIndex==undefined || $(Controls[j]).tabIndex>=0) {
                //    ControlsTabbed.push(Controls[j]);
                //}
                
                if (TrySetFocus($(Controls[j]))) {
                    ControlsTabbed.push(Controls[j]);
                }
                
            }
            Config.popups_ids[i].TabbedElements = ControlsTabbed;
         }
        
    }
    
    function CreateIndex(Config)
    {
        index = new Array();
        for (i=0;i<Config.nodes.length;i++) {
            index[Config.nodes[i].id]=i;   
        }
        Config.index = index;
    }
    
   
   /* 
    // Ставит на элемент фокус и, если он установился, возвращает true
    //   иначе возвращает false
    function TrySetFocus(ElementToFocus)
    {
        // Если у элемента класс "close" - не фокусируемся
        if ($(ElementToFocus).hasClass('close')) {
            return false;
        }
        
        // Не разрешаем ставить фокус на спрятанные
        if ($(ElementToFocus).is('hidden')) {
            return false;
        }
        
        // Ставим на элемент фокус
        $(ElementToFocus).focus();
        
        // Проверяем, установился ли он
        if (!$(ElementToFocus).is(':focus')) {
            return false;
        }
        return true;
    }
    */
    function ChooseTabElementsInContainer(Container)
        {
            var Result =
            //$(Container).find("a, input, select, button, textarea");
            $(Container).find("a, input[type!=hidden], select, button:not(.close), textarea");
            return Result;
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
        var PopupOpened = null;// Открытый поп-ап (если он открыт)
        var LastFocusedElement = null;// Элемент, на котором последний раз был фокус перед открытием поп-апа
        var LastFocusedBlock = null;// Блок, который был последним активен перед открытием поп-апа
        //  Сделано для того, чтобы после закрытия поп-апа восстановить фокус
        var ShiftWasPressed = false;// Флаг о том, что был нажат shift.
        // Сделано для того, чтобы запретить обратный shift
        var FocusedObject;// Ссылка на текущий объект, который находитс в фокусе
        var WasCountingTabElements = false;
        
        // Ставим обработчик, который будет сохранять ссылку на текущий объект в фокусе
        $('html').on('focus', 'a, input, select, button, textarea',function(){
            FocusedObject = this;
            })
        
         
                 // Ставим фокус на первый элемент первого блока
        // Выберем все элементы для блока
        function InitFirstActiveBlock(NodeToActive)
        {
            var TabElements = ChooseTabElementsInContainer(NodeToActive);
            
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
        }
         
    function TrySetFocusInternal(ElementToFocus, HiddensTry)
    {
        // Если у элемента класс "close" - не фокусируемся
        if ($(ElementToFocus).hasClass('close')) {
            return false;
        }
        
        // Не разрешаем ставить фокус на спрятанные
        /*if ($(ElementToFocus).is('hidden')) {
            return false;
        }
        */
        
        if (!HiddensTry) {
            if ($(ElementToFocus).attr('type')=='hidden') {
                return false;
            }
        }

        
        if ($(ElementToFocus)[0].tabIndex<0) {
            return false;
        }
        
        // Ставим на элемент фокус
        $(ElementToFocus).focus();
        
        // Проверяем, установился ли он
        //if (!$(ElementToFocus).is(':focus')) {
        if ($(ElementToFocus)[0]!=FocusedObject) { 
            return false;
        }
        
        // Если у элемента до инициализации системы стоял отрицательный таб-индекс - возвращаем false
        
        
        for (j=0;j<config.negativeTabIndex.length;j++)
        {
            if ($(ElementToFocus)[0]==config.negativeTabIndex[j]) {
                return false;
            }
        }
        
        return true;
    }
         
         // Ставит на элемент фокус и, если он установился, возвращает true
    //   иначе возвращает false
    function TrySetFocusHiddens(ElementToFocus)
    {
        return TrySetFocusInternal(ElementToFocus,true);
    }   
        
         // Ставит на элемент фокус и, если он установился, возвращает true
    //   иначе возвращает false
    function TrySetFocus(ElementToFocus)
    {
        return TrySetFocusInternal(ElementToFocus,false);
    }
        
        // Запрещает табуляцию на всех элементах кроме тех, которые находятся внутри активного
        //     блока (ставит tabIndex=-1)
        function ProhibitTabulation(NeedRecountProbibites)
        {
            
            // Если не открыт поп-ап.
            //    (при для поп-апов дейсвтия, аналогичные этой функции производятся
            //          в обработчике события открытия)
            if (!PopupOpened) {
                var ForFocusSaving;
             // Если разрешённые таб-элементы не просчитаны - просчитываем
                if ((!config.wasInitedNotTabbedElements)||(NeedRecountProbibites))
                {
                    // Сохраняем элемент на котором был фокус
                    ForFocusSaving = FocusedObject;
                    // Берём все элементы на странице, которые вообще могут быть в фокусе
                    var AllElements = ChooseTabElementsInContainer($('html'));
                 var ElementsNotTabbedBeginner = new Array();
                
                 // Перебираем все элементы на странице, которые вообще могут быть в фокусе
                  for (i=0;i<AllElements.length;i++ )
                  {
                     if (!TrySetFocusHiddens($(AllElements[i]))) {
                    // if (AllElements[i].tabIndex<0){ 
                        ElementsNotTabbedBeginner.push(AllElements[i]);
                    }

                }
           
                config.NotTabbed = ElementsNotTabbedBeginner;
                config.wasInitedNotTabbedElements = true;
                
                ForFocusSaving.focus();
            }
 
            
            //$('a, input, select, button, textarea').attr('tabindex','-1');
            ChooseTabElementsInContainer($('html')).attr('tabindex','-1');
            //$('*').attr('tabindex','-1');
            //$('body').attr('tabindex','-1');
            var ElementsOfActiveBlock = null;
            if (PopupOpened) {
                ElementsOfActiveBlock = ChooseTabElementsInContainer(PopupOpened);
                //ChooseTabElementsInContainer(PopupOpened).attr('tabindex','0');
            }
            else
            {
                ElementsOfActiveBlock = ChooseTabElementsInContainer($(currentNode.node)[0]);
                //ChooseTabElementsInContainer($(currentNode.node)[0]).attr('tabindex','0');
            }
            
            
            // Перебираем элементы текущего активного блока, проверяем,
            //   не запрещена ли ни них была табуляция изначально
            // Если нет - то ставим TabIndex = 0 каждому элементу
            if (ElementsOfActiveBlock) {
                // Перебираем элементы активного блока
                for (i=0;i<ElementsOfActiveBlock.length;i++)
                {
                    var IndexWasSet = false;//  Флаг о том, что на элементе в цикле был
                    //   поставлен отрицательный табиндекс
                    
                    
                    // Перебираем элементы, на которых изначально была запрещена табуляция
                    for (j=0;j<config.NotTabbed.length;j++)
                       {
                        // Если табуляция на элементе была изначально запрещена - ставим tabIndex=-1
                        if (ElementsOfActiveBlock[i]==config.NotTabbed[j]) {
                              $(ElementsOfActiveBlock[i]).attr('tabindex','-1');
                              
                              // Выходим из цикла
                              IndexWasSet = true;
                              break;
                            
                        }
                        
                        
                     }
                    
                     // Если не был поставлен отрицатенльный табиндекс 
                      if (!IndexWasSet) {
                          $(ElementsOfActiveBlock[i]).attr('tabindex','0');
                     }
                    
                  }
                }
            }
           
            
        }
        
           
              
               
        // ===========================
        // ===========================
        // Прочитываем из конфигурации id-шики поп-апов и привязываемся на их события показа и скрытия,
        //   Чтобы работать с фокусом и активными блоками
        for (i=0;i<config.popups_ids.length;i++)
        {
            (function (PopupId)
            {
                $(PopupId).on('show.bs.modal', function (e)
                    {
                        ProhibitTabulation();
                    }
                );
                 $(PopupId).on('shown.bs.modal', function (e)
                    {
                            /*   
                            $(PopupId).on('focus', 'a, input, select, button, textarea',function (e)  {
                                console.log('keydown fires');
                                PressTabControlHandler(e);
                            });
                        */
                            // Надо сохранить элемент, на котором был поставлен фокус
                            LastFocusedElement = $(':focus');
                       
                            // Сохраняем ссылку на открывшийся поп-ап
                            PopupOpened = $(PopupId)
                            
                            // Пересчитаем закрытые элементы заново
                            // ProhibitTabulation(true);
                            
                            // Ставим фокус на первый элемент открывшегося поп-апа
                            
                            //
                            ChooseTabElementsInContainer($('html')).attr('tabindex','-1');
                            
                            // Снимаем tabIndex
                            ChooseTabElementsInContainer($(PopupOpened)[0]).attr('tabindex','0');
                            
                            // Выберем все элементы для блока
                             TabElements = ChooseTabElementsInContainer($(PopupOpened)[0]);
        
                            // Ставим фокус на первый элемент поп-апа
                            for (i=0;i<TabElements.length;i++)
                            {
                                if (TrySetFocus(TabElements[i]))
                                {
                                    //Проверим, не входит ли элемент на котором сфокусировались в список тех,
                                    //  изначально был отрицательный tab-index
                                    var Negative = false;
                                    for (j=0;j<config.negativeTabIndex.length;j++)
                                    {
                                        if (TabElements[i]==config.negativeTabIndex[j]) {
                                            Negative = true;
                                            break;
                                        }
                                    }
                                    
                                    /*
                                        // Проверим, не входит ли данный элемент в список запрещённых для tabIndex-а
                                        var ProhibitedToTabIndex = false;
                                            
                                        // Перебираем элементы, на которых изначально была запрещена табуляция
                                        for (j=0;j<config.NotTabbed.length;j++)
                                        {
                                            // Если табуляция на элементе была изначально запрещена - ставим tabIndex=-1
                                            if (TabElements[i]==config.NotTabbed[j]) {
                                                ProhibitedToTabIndex = true;
                                                break;
                            
                                            }
                        
                        
                                        }  
                                        
                                        // Еслм на элемент можно сфокусироваться и он не был закрыт изначально -
                                        //     - выходим из цикла, мы нашли элемент, на который можно сфокусироваться
                                        if (!ProhibitedToTabIndex)
                                        {
                                            break; 
                                        }
                                        */
                                    if (!Negative) {
                                        break;
                                    }
                                }
                            }
                           // ProhibitTabulation(true);
                            //alert('Стыдно когда видно ' +PopupId);
                            
                    }
                );
            
            
                $(PopupId).on('hidden.bs.modal', function (e)
                    {
                        
                        // Обнуляем ссылку на по-ап
                        PopupOpened = null;                        
                        
                        // Снимаем tabIndex
                        //ChooseTabElementsInContainer($('html')).attr('tabindex','0');
                        ProhibitTabulation();
                        /*
                        // Восстанавливаем фокус на странице
                        $(LastFocusedElement)[0].focus();
                        
                        // Пересчитаем tab-индекс
                        
                        */
                        
                        // Ставим фокус на первый элемент
                        InitFirstActiveBlock(currentNode.node);
                        
                        

                    }
                );
                
            }('#'+config.popups_ids[i].id));    
            
        }   
        //=================================
        //====== Ставим перехват события click на document
        //  внутри которого смотрим элемент, на который мы кликнули и выделяем тот узел графа, в котором
        //    находится элемент, на который мы кликнули
        
         $(document).on('click', function(e) {
            // e.target - объект, на который кликнули и который нам надо проверить
            
            // Для элемента e.target берём его непосредсвенных родителей и
            //  1. Поднимаемся по иерархии родителей, пока родитель не станет равным <html>
            //  2. Для каждого родительского уровня перебираем узлы конфигурации и
            //          Если родитель на очередном уровне равен узлу графа, то мы
            //       1. Выделяем данный узел цветом
            //       2. Помечаем его текущим
            
            var Ancestor = $(e.target).parent(); // (Предок)
            var Patriarch = $('html'); // (Патриарх) - самый первый элемент на странице :)
            var FoundNode = false;
            var NewNode;
            
            while ((Ancestor[0])!=(Patriarch[0])) // Пока предок не равен патриарху :)
            //    (Пока не исчерпали всех родителей)
            {
                FoundNode = false;
                
                // Перебираем узлы
                for (i=0;i<config.nodes.length;i++)
                {
                    if (Ancestor[0]==$(config.nodes[i].node)[0]) {
                        // Ура!!! Мы нашли узел
                        //  Поднимаем флаг и вызываем break
                        NewNode = config.nodes[i];
                        FoundNode = true;
                        break;
                    }
                }
                
                if (FoundNode ) {
                    break;
                }
                
                Ancestor = Ancestor.parent();
                
            }
            
            
            // Здесь проверяем, если флаг найденного узла взведён - выделяем узел

            if (FoundNode) {
                // Проверяем - если данный узел равный текущему, то выходим - делать больше нечего :)
                if (NewNode == currentNode) {
                    return;
                }
                
                // Ну а коль скоро узел поменялся - выделяем его
                $(currentNode.node).trigger('blur');
                // Для некорневого узла убираем подсветку при переходе
                $(currentNode.node).removeClass('background-keyboard-plugin');
                
                $(NewNode.node)
                    .focus()
                    .addClass('background-keyboard-plugin');
                if (NewNode.hasOwnProperty('handler')) {
                        NewNode.handler();
                }
                
                PreviousNode = currentNode;
                currentNode = NewNode;
                
                //  Пытаемся поставить фокус на элемент, на который произошёл клик.
                //     Если не удаётся-ставим на первый элемент блока, на который это можно сделать
                if(!TrySetFocus(e.target))
                {
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
                }
                
                
            }
            
         });
        
        
        //=================================
        
        // Ссылка "Помощь по клавиатуре"
        var link = '.keyboard-help-link';
        
        $(link).popover({
            placement: 'bottom',
          //  placement: 'right',
            html: true,
          //  top: '0px',
            content: function() {
                return $(".pop-keyboard-help").html();
            }
        });
        
        
        // Ставим обработчик, цель которого - проверить, если была отпущена мастер-клавиша,
        //  то надо сбросить соответствующий флаг и удалить сообщения с подсказками из
        //    поп-апа обновить этот поп-ап
        $(document).on('keyup', function(e) {
            // Если был отпущен shift - это надо обработать
            if (e.keyCode==16) {
                ShiftWasPressed = false;
            }
            
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
        /*    
        // Ставим фокус на первый элемент первого блока
        // Выберем все элементы для блока
        function InitFirstActiveBlock(NodeToActive)
        {
            var TabElements = ChooseTabElementsInContainer(NodeToActive);
            
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
        }
        */
        InitFirstActiveBlock($(currentNode.node)[0]);
        LastFocusedBlock = currentNode;
            
       // Подвешиваем на все инпуты и селекты в форме обработчик keydown,
       //   который делает следующие вещи:
       //   Считывает все элементы текущего блока
       //    Пробегается по ним, выясняет, какой из них в фокусе (
       //        с помощью глобальной ссылки на элемент, находящийся в фокусе на странице)
       //      если индекс такого элемента равен длине массива элементов блока или не найден элемент в фокусе -
       //      то ставим фокус на первый элемент блока
       //    
    

            

                   $('html').on('keydown', 'a, input, select, button, textarea',function (e)  {
                console.log('keydown fires');
                PressTabControlHandler(e);
            });

        

    function ChangeFocus()
    {
        
    }
        
    // Обрабатывает нажатие таб на контроле. Сделано для того, чтобы после нажатия таба
    //    на последнем контроле в форме был переход фокуса
    //   на первый контрол той же формы
    function PressTabControlHandler(Target)
    {
              
        // Если нажат таб
        if (Target.keyCode==9)
        {
                ProhibitTabulation();



             // Получаем для текущего активного узла элементы, по которым можно переходить по табу
            var TabElements = null;
            // Если открыт поп-ап, то читаем все элементы по которвм можно ходить табом в поп-апе
            if (PopupOpened) {
                TabElements = ChooseTabElementsInContainer(PopupOpened);
            }
            else
            {
                // Иначе читаем все элементы  по которвм можно ходить табом в текущем блоке
                TabElements = ChooseTabElementsInContainer($(currentNode.node)[0]);
            }   
            
            // Вычисляем индекс элемента, находящегося в фокусе в текущем блоке
            var LastTabIndex = 0;
            
            // Пробегаем по элементам, по которым можно ходить табом в данном блоке или поп-апе
            while (LastTabIndex<TabElements.length)
            {
                // Если текущий элемент равен элементу, который находится в фокусе на странице, то выходим
                if ($(TabElements[LastTabIndex])[0]==FocusedObject) {
                    break;
                }
                
                LastTabIndex++;
            }
            
            // Если был зажат Shift
            if (ShiftWasPressed) {
                var Success = false;
                // Перебираем элементы те, которые стоят перед активным и пытаемся ставить на них фокус
                for (i=LastTabIndex-1;i>=0;i--) {
                    if (TrySetFocus(TabElements[i])) {
                        Success = true;
                        break;
                    }
                }
                
                // Если фокус так и не поставился - перебираем с самого последнего индекса
                if (!Success) {
                    for (i=TabElements.length-1;i>=0;i--) {
                        if (TrySetFocus(TabElements[i])) {
                            break;
                        }
                    }
                }
                
                Target.preventDefault();
                return false;
                
            }
            // Иначе ведём перебор элементов в другую сторону
            else
            {
                var Success = false;
                // Перебираем элементы те, которые стоят после активного и пытаемся ставить на них фокус
                for (i=LastTabIndex+1;i<TabElements.length;i++) {
                    if (TrySetFocus(TabElements[i])) {
                        Success = true;
                        break;
                    }
                }
                
                // Если фокус так и не был поставилен - начинаем с первого
                if (!Success) {
                    for (i=0;i<TabElements.length;i++) {
                        if (TrySetFocus(TabElements[i])) {
                            break;
                        }
                    }
                }
                Target.preventDefault();
                return false;
            }
            
            
            // Проверяем - если LastTabIndex нулевой и был зажат Shift, то надо сбросить нажатие клавиши
            // Иначе - если LastTabIndex последний и шифт не зажат - надо тоже погасить событие и поставить в фокус
            //   первый элемент
            /*
            if (LastTabIndex==0 && ShiftWasPressed) {
                for (i=TabElements.length-1;i>=0;i--) {
                    if (TrySetFocus(TabElements[i])) {
                        break;
                    }
                }
                Target.preventDefault();
                return false;
                
            }
            else
            {
                if (LastTabIndex==TabElements.length-1 && !ShiftWasPressed) {
                    for (i=0;i<TabElements.length;i++) {
                        if (TrySetFocus(TabElements[i])) {
                            break;
                        }
                    }
                    Target.preventDefault();
                    return false;
                    
                }
            }
            */
            // 
            
            /*
            // Наглым образом запрещаем обратную табуляцию
            if (ShiftWasPressed) {
                Target.preventDefault();
                return false;
            }
            
            // Получаем для текущего активного узла элементы, по которым можно переходить по табу
            
            var TabElements = null;
            // Если открыт поп-ап, то читаем все элементы по которвм можно ходить табом в поп-апе
            if (PopupOpened) {
                TabElements = ChooseTabElementsInContainer(PopupOpened);
            }
            else
            {
                // Иначе читаем все элементы  по которвм можно ходить табом в текущем блоке
                TabElements = ChooseTabElementsInContainer($(currentNode.node)[0]);
            }            
            
            // Вычисляем индекс элемента, находящегося в фокусе в текущем блоке
            var LastTabIndex = 0;
            
            // Пробегаем по элементам, по которым можно ходить табом в данном блоке или поп-апе
            while (LastTabIndex<TabElements.length)
            {
                // Если текущий элемент равен элементу, который находится в фокусе на странице, то выходим
                if ($(TabElements[LastTabIndex])[0]==FocusedObject) {
                    break;
                }
                
                LastTabIndex++;
            }
            
            // Если фокусированный элемент не последний и он есть, то вызываем фокус на следующий элемент
            if (LastTabIndex<TabElements.length-1) {
                TabElements[LastTabIndex+1].focus();
            }
            else
            {
                // Иначе пытаемся поставить фокус в элемент начиная с первого
                for (i=0;i<TabElements.length;i++) {
                    if (TrySetFocus(TabElements[i])) {
                        break;
                    }
                }
            }
            
            Target.preventDefault();
        */
        }
        
        
    }  
            
        $(currentNode.node).addClass('background-keyboard-plugin');
            // Ставим упоминание о мастер-клавише
            var rootKeyElement = $('<div>').addClass('masterkey');
            $(rootKeyElement).text('Нажмите и держите клавишу ESC для перемещения');
            $(".pop-keyboard-help").append(rootKeyElement);

        /*
        // Выбирает все элементы в контейнере, на которые можно перейти табулятором
        function ChooseTabElementsInContainer(Container)
        {
            var Result = 
            //$(Container).find("a, input, select, button, textarea");
            $(Container).find("a, input, select, button, textarea");
            return Result;
        }
        */
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
                        
                        // Если был зажат шифт - это надо запомнить
                        if (e.keyCode==16) {
                            ShiftWasPressed = true;
                        }
                        
                        // Если был открыт поп-ап, то выходим
                        if (PopupOpened) {
                            return;
                        }
                        
                        // Если нажата мастер-клавиша - ставим флажок и обновляем поп-ап
                         if(e.keyCode == MasterKeyCode ) {
                            MasterKeyWasPressed = true;
                            ProhibitTabulation();
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
                            LastFocusedBlock = currentNode;
                            
                            $(':focus').blur();
                            ProhibitTabulation();
                            // Ставим фокус на первый элемент блока
                            var TabElements = ChooseTabElementsInContainer($(currentNode.node)[0]);
                            for (i=0;i<TabElements.length;i++)
                            {
                                if (TrySetFocus(TabElements[i])) {
                                    {
                                        // Проверим, не входит ли данный элемент в список запрещённых для tabIndex-а
                                        var ProhibitedToTabIndex = false;
                                            
                                        // Перебираем элементы, на которых изначально была запрещена табуляция
                                        for (j=0;j<config.NotTabbed.length;j++)
                                        {
                                            // Если табуляция на элементе была изначально запрещена - ставим tabIndex=-1
                                            if (TabElements[i]==config.NotTabbed[j]) {
                                                ProhibitedToTabIndex = true;
                                                break;
                            
                                            }
                        
                        
                                        }  
                                        
                                        // Еслм на элемент можно сфокусироваться и он не был закрыт изначально -
                                        //     - выходим из цикла, мы нашли элемент, на который можно сфокусироваться
                                        if (!ProhibitedToTabIndex)
                                        {
                                            break; 
                                        }
   
                                    }
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
        
        // Поп-ап с помощью. может пригодиться
        /*$(link).on('click', function(e) {
            WasPopupOpened = true;
            $(this).popover('show');
        }).on('blur', function() {
            WasPopupOpened = false;
            $(this).popover('hide');
        });
        
        */
        
        $(link).on('click', function(e) {
            WasPopupOpened = true;
            $(this).popover('show');
        });
    }
});

// Модуль, выводящий пагинацию для списков
//$(document).ready(function(e) {

    // Общие настройки для пагинации 
    commonPaginationConfig = {
        'ClickablePagesCount':5,
        // Количество ссылок на страницы, сколько одноврменно
        //   может быть выведено в список пагинации
    
        'RowsPerPage':5, // Количество записей на одной странице списка

    }
    
    // Контейнер для конфигурации пагинации для каждого отдельного списка.
    //   Содержит для каждого списка данные, необходимые поддержки его пагинации
    //    (на странице может быть несколько списков с пагинацией)
    //    Перед загрузкой списка необходимо добавить данные для него путём вызова функции
    //    InitPaginationList для каждого из списка на странице. При вызове подаётся id таблицы со списком
    //      и необходимые данные
    PaginationSettingsContainer =
    {
       
    }

    // Инициализация данных для управление пагинацией
    // TableListId - ID таблицы со списком
    // SortName - поле, по которому идёт сортировка
    //  SortOrder - порядок сортировки
    //  OnPaginationChange - функция обновления списка. Подаётся, чтобы при
    //    нажатие на кнопку пагинации можно было вызвать перезагрузку списка
    function InitPaginationList(TableListId, SortName,SortOrder,OnPaginationChange)
    {
        PaginationSettingsContainer[TableListId] =
        {
            'sortOrder': SortOrder,
            'sortName': SortName,
            'paginationChangeHandler': OnPaginationChange,
            'activePage': 1,
        };
     
        
    }

    //  По идентификатору списка возвращает параметры для пагинации в виде строки для вставки в урлу
    function getPaginationParameters(ListSelector)
    {
        var PaginationObject = PaginationSettingsContainer[ListSelector];
        var Result = '';
        if (PaginationObject!=undefined) {
            	/*$rows = $_GET['rows'];
                $page = $_GET['page'];
                $sidx = $_GET['sidx'];
                $sord = $_GET['sord'];
                */
                
                // Записываем параметры в результат
                Result = Result+
                    'rows='+commonPaginationConfig.RowsPerPage+
                    '&page='+PaginationObject.activePage+
                    '&sidx='+PaginationObject.sortName+
                    '&sord='+PaginationObject.sortOrder
                ;
            
            
        }
        
        
        return Result;
    }

    // Выполняет переход на страницу при нажатии одной из кнопок
    //   ListId - имя списка для перехода
    //   PageNumber - номер страницы, на которую нужно перейти
    function goNewPage(ListId,PageNumber)
    {
        // Записываем в конфигурацию пагинации номер следующей активной страницы
        PaginationSettingsContainer[ListId].activePage = PageNumber;
        
        // Вызываем хендлер "обновить список"
        PaginationSettingsContainer[ListId].paginationChangeHandler();
        
        
        
    }

    // Возвращает пустую кнопку для пагинации
    function createEmptyPaginationButton()
    {
        var Result = $('<li>');
        var InnerLink = $('<a>')
        $(InnerLink).attr('href','#');
        $(InnerLink).appendTo(Result);
        
        return Result;
    }

    // Замыкание данных, нужных при обработке нажатия клавиши пагинации
    function closeTablePage(ListSelector,NewPageNumber, PaginationButton)
    {
        $(PaginationButton).on('click',function(e)
                               {
                                    goNewPage(ListSelector,NewPageNumber);
                                    return false;
                               }
                               
                               );
    }
    
    // Создаёт и добавляет в контейнер кнопку пагинации
    function createOnePaginationButton(Container,table,ButtonText,PageNumber)
    {
        // Создадим кнопку
            var SomeButton = createEmptyPaginationButton();
            $(SomeButton).children('a').text(ButtonText);
            
            // Замыкаем ид таблицы и номер страницы
            closeTablePage(table,PageNumber,$(SomeButton));
            
            // Добавляем кнопку в список
            $(SomeButton).appendTo($(Container));
        
    }

    // Выводит пагинацию в списках, построенных на клиенте на основании Ajax-запросов
    // table - элемент tbody таблицы, содержащей список
    //    handler - функция вывода списка заново. Вызывается при нажатии на одну из кнопок пагинации
    //    totalPagesCount - общее количество страниц
    //   sortFieldName и sortOrder - устанавливаются снаружи при вызове функции
    function printPagination(table, totalPagesCount)
    {
        var NeedWriteFirstPageButton = true; // Нужно ли вывести кнопку "идти на первую страницу"
        var NeedWriteLastPageButton= true; // Нужно ли вывести кнопку "идти на последнюю страницу"
        
        var NeedWriteNextPageButton= true; // Нужно ли вывести кнопку "идти на следующую страницу"
        var NeedWritePrevPageButton= true; // Нужно ли вывести кнопку "идти на предыдущую страницу"
        
        // Выбираем по элементу table тот контейнер с пагинацией, принадлежащий списку table
        var PaginationContainer = $('#'+table+' tbody').parents('div').next().children('ul');
    
        // Делаем невидимым контейнер с пагинацией (вдруг его надо спрятать)
        $(PaginationContainer).parent('div').addClass('no-display');
    
        // Убиваем все элементы li - чтобы вывести их заново
        $(PaginationContainer).children('li').remove();
    
        // Если записей нет - выходим, не нужно выводить пагинацию
    
        if (totalPagesCount==0) {
            return;
        }
        
        // Читаем текущую страницу из спана current-page-number
        var CurrentPageNumber = PaginationSettingsContainer[table].activePage;
        
        // Теперь нужно вывести кнопочки для пагинации с соответсвующими caption-ами
        
        // Вычисляем, начиная с какой цифры нужно выводить цифровые кнопочки.
        //      (и по какую)
        var BeginIndex = CurrentPageNumber - Math.floor(commonPaginationConfig.ClickablePagesCount/2);
        var EndIndex = CurrentPageNumber + Math.floor(commonPaginationConfig.ClickablePagesCount/2);
        
        // Если получился отрицательный начальный индекс - сдвигаем индексы вправо |BeginIndex|+1
        if (BeginIndex<=0) {
            EndIndex += Math.abs(BeginIndex)+ 1;
            BeginIndex =1;
        }
        
        // Если правый индекс получился больше, чем totalPagesCount
        //    - Чтобы не вывести больше страниц, чем нужно 
        if (EndIndex>totalPagesCount) {
            EndIndex = totalPagesCount;
        }
        
        // Если страница первая - то не надо выводить кнопку <<
        if (CurrentPageNumber==1) {
            NeedWriteFirstPageButton = false;
        }
        
        // Если страница - последняя - то не надо выводить кнопку >>
        if (CurrentPageNumber==totalPagesCount) {
            NeedWriteLastPageButton = false;
        }
        
        
        if (NeedWriteFirstPageButton) {
            // Добавляем в контейнер кнопку <<
            createOnePaginationButton(PaginationContainer,table,'<<',1);
            
            
        }
        
        
        // Выводим цифровые кнопки
        for (i=BeginIndex;i<=EndIndex;i++)
        {
            createOnePaginationButton(PaginationContainer,table,i,i);
            
            // Если i равен индексу активной страницы
            if (i==CurrentPageNumber) {
                // Ставим последнему детю контейнера класс "active"
                $(PaginationContainer).children(':last').addClass('active');
            }
            
        }
        
        if (NeedWriteLastPageButton) {
            // Добавляем в контейнер кнопку >>
            createOnePaginationButton(PaginationContainer,table,'>>',totalPagesCount);
        }
        
        // Выводим пагинацию
        $(PaginationContainer).parent('div').removeClass('no-display');
    }
//});
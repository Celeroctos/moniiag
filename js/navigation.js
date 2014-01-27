/*
  Модуль работает следующим образом:
  При загрузке выбираются все ссылки из меню в один массив (дерево преобразуется в массив)
  При активизации режима навигации определяется первая снизу активная ссылка
  (снизу, потому что jquery обходит от корня к листу при преобразовании дерева к массиву)
  , индекс этой ссылки хранится в специальной переменной. 
  При нажатие на стрелочки вверх и вниз, индекс увеличивается
  (или уменьшается) и по нему ставится класс ссылке из массива
  
  
*/

$(document).ready(function() {
    
    var NavigationModeOn = false;
    
    // Выбираем Всё ссылки из меню
    var Links = $('ul.bs-sidenav').find('li a');
    
    // Индекс текущей активной ссылки
    var LinksPointer = -1;
        
    this.initNavSystem = function()
    {
        
        
        
        function TurnOffNavigation()
        {                                   
            // Снимаем класс у активного элемента
            $('.current-sidebar-menu').removeClass('current-sidebar-menu');            
            // Сбрасываем флаг
            
            
            NavigationModeOn = false;
        }
        
        function OnLinkIndexChanged()
        {
             $(Links).removeClass('current-sidebar-menu');
             //Links[LinksPointer].setAttribute('class', Links[LinksPointer].getAttribute('class')+' ' + 'current-sidebar-menu');
             
             $($(Links)[LinksPointer]).addClass('current-sidebar-menu');
        }
        
        function TurnOnNavigation()
        {
            // Сначала нужно выяснить - какой пункт меню нужно выделить 
            //    при включении режима навигации
            if (LinksPointer<0)
            {
            // Пробегаемся по массиву ссылок снизу вверх и смотрим класс active у родителя.
            //   Первый элемент у которого родитель активный - это ссылка, которая должна быть активна при
            //    включения режима навигации
            
                for (i=Links.length;i>=0;i--)
                {
                    if ($(Links[i]).parent().hasClass('active'))
                    {
                        LinksPointer = i;
                        break;
                    
                    }
                
                }
            }
            
            // Выделяем пункт меню
            OnLinkIndexChanged();
            
             
            // Ставим флаг
            NavigationModeOn = true;
        }
        
        function ControlKeyDown(Target)
        {
            // Если клавиша "стрелка назад"
        
            switch (Target.keyCode)
            {
                // Включить навигацию
                case 37:
                                 // Переключаем режим только если курсор стоит в начале контрола
                if (Target.currentTarget.selectionStart==0)
                {
                    if (!NavigationModeOn)
                    {
                        TurnOnNavigation();
                        
                    }
                    
                }
                break;
                
                
                // Выключить навигацию
                case 39:
                     if (NavigationModeOn)
                  {
                      TurnOffNavigation();
                      
                  }
                break;
                
                // Стрелка вверх
                case 38:
                
                if (NavigationModeOn)
                {
                     if (LinksPointer>0)
                     {
                         LinksPointer--;
                         OnLinkIndexChanged();
                     }
                    
                }
                
                
                break;
                // Стрелка вниз
                case 40:
                if (NavigationModeOn)
                {
                     if (LinksPointer<Links.length-1)
                     {
                         LinksPointer++;
                         OnLinkIndexChanged();
                     }

                } 
                break;
                
                // По клавише Enter будет переход про пункту меню
                case 13:
                
                if (NavigationModeOn)
                {
                     // Берём активную ссылку и перехождим по ней
                     $($(Links)[LinksPointer])[0].click();
                    
                }
                
                break;
                
                
            }
        }
        
        // Инициализируем обработчик события нажатия клавиши на всех контролах в странице.
        //    в этом обработчике - проверяем, если нажата клавиша стрелки назад - 
        //    запускаем режим навигации:
        //     Ищем самый вложенный активный пункт меню, выделяем его
        
        // Если есть панель навигации
        if ($('.bs-sidebar').length>0)
        {
        // Выбираем инпуты и селекты
        var ControlsInput = $('input');
        var ControlsSelect = $('select');
        
        
        
        for (i=0;i<ControlsInput.length;i++)
                 {
                        (function (Control)
                        {
                            $(Control).on('keydown',function (e)
                            {
                                ControlKeyDown(e);
                            });
                        })(ControlsInput[i]);
                 }
                 
                 // Перебираем все контролы с селектами
                 for (i=0;i<ControlsSelect.length;i++)
                 {
                        (function (Control)
                        {
                            $(Control).on('keydown',function (e)
                            {
                                ControlKeyDown(e);
                            });
                        })(ControlsSelect[i]);
                 }
        
            // Выбираем Всё ссылки из меню
            var Links = $('ul.bs-sidenav').find('a');
            console.log(Links);
            
        
        }  
        
    };   
    
     this.initNavSystem();
    
});
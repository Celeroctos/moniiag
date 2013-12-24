keyboard_cnf =
{
    'page_configs':[
     {
            'page_key':'\.php/test',
            'page_config':[
            ]
     },
     {
        'page_key':'\.php/reception/patient/viewsearch',
        'page_config':[
            {
                'node' : '#mainSideMenu', // Здесь селектор для ноды
                'key' : 37, // Здесь код для клавиши
                'keyDesc' : 'Стрелка влево - переход в меню', // Здесь описание клавиши в подсказке
                'handler' : function() { // Хандлер, исполняющийся при переходе на элемент
                    //alert(1);
                },
                'children' :
                    [
                        // Это узлы-потомки
                        {
                            'node' : '#mainSideMenu',
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
                'node' : '#patient-search-form',
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
        
    },

     
    ]
}
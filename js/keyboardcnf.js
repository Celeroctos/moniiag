keyboard_cnf =
{
    'page_configs':[
     {
            'page_key':'\.php/test',
            'nodes':
            [
                
            ]
     },
     {
        'page_key':'\.php/admin/modules/shedulesettings',
        'nodes':
        [
            {
                'node' : '#shedule-settings-block', // Здесь селектор для ноды
                'id': '1',
                'handler' : function() { // Хандлер, исполняющийся при переходе на элемент
                            //alert(1);
                },
                'ways':
                [
                    {
                        'target':'2',
                        'key': 40, 
                    },
                                        {
                        
                        'key': 18,
                        'handler' : function() { // Хандлер, исполняющийся при переходе на элемент
                            alert("Хендлер дуги сработал");
                        },
                    }
                ]
                
            },
            {
                'node' : '#gbox_shifts', // Здесь селектор для ноды
                'id': '2',
                'handler' : function() { // Хандлер, исполняющийся при переходе на элемент
                            //alert(1);
                },                
                'ways':
                [
                    {
                        'target':'1',
                        'key': 38,
                    }
                ]
            }            
        ]
        
    },

     
    ]
}

/*
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

*/
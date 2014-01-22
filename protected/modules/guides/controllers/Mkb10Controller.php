<?php
class Mkb10Controller extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $this->render('view', array());
    }

    public function actionGet($nodeid) {
        if(trim($nodeid) == '') {
            $nodeid = 0;
        }

        $rows = $_GET['rows'];
        $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];
        // Выборка только "любимых" диагнозов
        if(isset($_GET['onlylikes'])) {
            $onlylikes = $_GET['onlylikes'];
        } else {
            $onlylikes = false;
        }

        // ID медработника
        if(isset($_GET['medworkerid'])) {
            if($_GET['medworkerid'] != -1) {
                $medworkerId = $_GET['medworkerid'];
            } else {
                $medworkerId = false;
            }
        } else {
            $medworkerId = Yii::app()->user->medworkerId;
        }

        $model = new Mkb10();
        if(isset($_GET['listview']) && $_GET['listview'] == 1) {
            $num = $model->getNumRows($onlylikes, $medworkerId);
        } else { // В виде дерева
            $num = count($model->getRowsByLevel($onlylikes));
        }

        $totalPages = ceil($num / $rows);
        $start = $page * $rows - $rows;
        //var_dump($start);
        if(isset($_GET['listview']) && $_GET['listview'] == 1) {
            // Фильтры поиска
            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }
            $limit = $_GET['limit'];
            $mkb10 = $model->getRows($onlylikes, $filters, $medworkerId, $sidx, $sord, $start, $limit);
        } else {
            $mkb10 = $model->getRowsByLevel($onlylikes, $nodeid, $sidx, $sord, $start, $rows);
        }
        foreach($mkb10 as $key => &$node) {
            if(count($model->getRowsByLevel($onlylikes, $node['id'])) > 0) {
               $node['isLeaf'] = false;
            } else {
               $node['isLeaf'] = true;
            }
            $node['loaded'] = false;
            $node['expanded'] = false;
            $node['parent'] = $node['parent_id']; // Суррогат для схлопывания таблицы
        }
        //var_dump($mkb10);
        echo CJSON::encode(
           array('rows' => $mkb10,
                 'total' => $totalPages,
                 'records' => $num,
                 'success' => 'true')
        );
    }
}

?>
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

        $model = new Mkb10();
        $num = $model->getRowsByLevel();

        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;
        //var_dump($start);
        $mkb10 = $model->getRowsByLevel($nodeid, $sidx, $sord, $start, $rows);

        foreach($mkb10 as $key => &$node) {
           if(count($model->getRowsByLevel($node['id'])) > 0) {
               $node['isLeaf'] = false;
           } else {
               $node['isLeaf'] = true;
           }
            $node['loaded'] = false;
            $node['expanded'] = false;
        }
       // var_dump($mkb10);
        echo CJSON::encode(
           array('rows' => $mkb10,
                 'total' => $totalPages,
                 'records' => count($num))
        );
    }
}

?>
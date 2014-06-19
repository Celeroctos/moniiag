<?php
class AddressController extends Controller {
    public $layout = 'application.views.layouts.index';

    public function actionGetRegionForm() {
        echo CJSON::encode(array(
            'success' => true,
            'data' => $this->render('index', array(
                'widgetStr' => 'application.modules.guides.components.widgets.RegionFormAddWidget',
                'data' => array()
            ), true))
        );
    }

    public function actionGetDistrictForm() {
        echo CJSON::encode(array(
                'success' => true,
                'data' => $this->render('index', array(
                    'widgetStr' => 'application.modules.guides.components.widgets.DistrictFormAddWidget',
                    'data' => array()
                ), true))
        );
    }

    public function actionGetSettlementForm() {
        echo CJSON::encode(array(
                'success' => true,
                'data' => $this->render('index', array(
                    'widgetStr' => 'application.modules.guides.components.widgets.SettlementFormAddWidget',
                    'data' => array()
                ), true))
        );
    }

    public function actionGetStreetForm() {
        echo CJSON::encode(array(
                'success' => true,
                'data' => $this->render('index', array(
                    'widgetStr' => 'application.modules.guides.components.widgets.StreetFormAddWidget',
                    'data' => array()
                ), true))
        );
    }
}
?>
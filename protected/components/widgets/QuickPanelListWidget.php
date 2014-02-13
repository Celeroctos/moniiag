<?php
class QuickPanelListWidget extends CWidget {
    public function run() {
        $this->render('application.components.widgets.views.QuickPanelListWidget', array(
            'icons' => QuickPanelIcon::model()->getRows()
        ));
    }
}

?>
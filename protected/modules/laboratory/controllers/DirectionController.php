<?php

class DirectionController extends LController {

    public function actionView() {
        $this->render("view");
    }

    /**
     * Override that method to return controller's model
     * @return LModel - Controller's model instance
     */
    public function getModel() {
        return new LDirection();
    }
}
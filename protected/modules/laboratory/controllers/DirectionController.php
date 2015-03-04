<?php

class DirectionController extends LController {

	/**
	 * Default view action
	 */
    public function actionView() {
        $this->render("view");
    }

	/**
	 * Register some form's values in database, it will automatically
	 * fetch model from $_POST["model"], decode it, build it's LFormModel
	 * object and save into database. But you must override
	 * LController::getModel and return instance of controller's model else
	 * it will throw an exception
	 *
	 * @in (POST):
	 *  + model - String with serialized client form via $("form").serialize(), if you're
	 * 		using LModal or LPanel widgets that it will automatically find button with
	 * 		submit type and send ajax request
	 * @out (JSON):
	 *  + message - Message with status
	 *  + status - True if everything ok
	 *
	 * @see LController::getModel
	 * @see LModal
	 * @see LPanel
	 */
	public function actionRegister() {
		parent::actionRegister();
	}

	/**
     * Override that method to return controller's model
     * @return LModel - Controller's model instance
     */
    public function getModel() {
        return new LDirection();
    }
}
<?php

class LaboratoryModule extends WebModule {

	/**
	 * Override that method to return paths to javascript,
	 * css and less files for current module
	 */
	public function getClientScripts() {
		return [
			"core.js",
			"laboratory.js",
			"form.js",
			"laboratory.css"
		];
	}
}
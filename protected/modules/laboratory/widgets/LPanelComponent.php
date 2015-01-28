<?php

abstract class LPanelComponent extends LComponent {

    /**
     * Renders a view. The named view refers to a PHP script (resolved via {@link getViewFile})
     * that is included by this method. If $data is an associative array,
     * it will be extracted as PHP variables and made available to the script.
     * @param string $view - Name of the view to be rendered. See {@link getViewFile} for details
     * about how the view script is resolved.
     * @param array $data - Data to be extracted into PHP variables and made available to the view script
     * @param boolean $return Whether the rendering result should be returned instead of being displayed to end users
     * @return string - The rendering result. Null if the rendering result is not required.
     * @throws CException - If the view does not exist
     * @see getViewFile
     */
    public function render($view = null, $data = null, $return = false) {
        $panel = new LPanel();
        $panel->body = parent::render("LForm", $data, true);
        $panel->id = $this->id."-panel";
        $panel->title = $this->title;
        if ($panel->body instanceof LWidget) {
            $panel->body = $panel->body->run(true);
        }
        return $panel->render("LPanel", null, $return);
    }
}
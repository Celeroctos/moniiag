<?php

abstract class LComponentForm extends LComponent {

    public $title = null;
    public $id = null;
    public $body = null;

    /**
     * Render component which implements LComponent object class
     * @param LForm $form - Parent form component
     * @param null $data
     * @param bool $return
     * @return mixed
     */
    public function build($form, $data = null, $return = false) {
        $this->body = parent::render(get_class($form), $data, true);
        $this->id = $form->id."-modal";
        $this->title = $form->title;
        if ($this->body instanceof LWidget) {
            $this->body = $this->body->run(true);
        }
        return $this->render(get_class($this), null, $return);
    }
} 
<?
/**
 *   That file looks silly, cuz it avoids yii basic architecture, but its the easiest and most suitable way
 * to declare ModalWindow's render template to avoid html generation via <code>echo/print</code>. That file
 * invokes from ModalWindow component from it's <code>run</code> method
 *
 * @var $self - Reference to modal window instance, which implements next methods (abstract class ModalWindow) :
 *   1. getTitle - returns modal window title, which will be displayed in header
 *   2. getView - returns render path to modal window's view
 *   3. getData - returns data, which have to be sent into view renderer
 *   4. getModalID - returns an javascript element identifier
 *   5. getErrorClass - returns name of error class in popup window
 */
?>

<div class="modal fade <? print $self->getErrorClass(); ?>" id="<? print $self->getModalID(); ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <? print $self->getTitle(); ?>
                </h4>
            </div>
            <? $self->render($self->getView(), $this->getData()); ?>
        </div>
    </div>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12" style="text-align: center;">
            <?php echo CHtml::ajaxSubmitButton(
                'Удалить',
                CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/categories/remove'),
                array(
                    'success' => 'function(data, textStatus, jqXHR) {
                                $("#categorie-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                ),
                array(
                    'class' => 'btn btn-danger'
                )
            ); ?>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
        </div>
    </div>
</div>
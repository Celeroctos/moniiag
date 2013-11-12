<div class="navbar navbar-blue navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><?php echo Yii::app()->name ?></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="#">Справка</a></li>
                <li><a href="#">Об авторах</a></li>
            </ul>
            <form class="navbar-form navbar-right">
                <div class="form-group">
                    <input type="text" placeholder="Логин" class="form-control">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Пароль" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Войти</button>
            </form>
        </div>
    </div>
</div>
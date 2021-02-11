<section class="main">
    <div class="container">
        <div class="main__inner">
            <form class="form main__form" action="<?= PATH ?>new" method="POST">
                <ul class="form__items">
                    <li class="form__item">
                        <label class="form__item-wrapper">Имя
                            <input class="form__item-entry" type="text" name="name">
                        </label>
                    </li>
                    <li class="form__item">
                        <label class="form__item-wrapper">Размер поля
                            <select class="form__item-entry" name="mapSize">
                                <option class="form__item-entry-option" value="5x5">5 x 5</option>
                                <option class="form__item-entry-option" value="6x5">6 x 5</option>
                                <option class="form__item-entry-option" value="7x5">7 x 5</option>
                            </select>
                        </label>
                    </li>
                    <li class="form__item">
                        <label class="form__item-wrapper">Запас препятствий
                            <select class="form__item-entry" name="amount_obst">
                                <option value="1" class="form__item-entry-option">1</option>
                                <option value="2" class="form__item-entry-option">2</option>
                                <option value="3" class="form__item-entry-option">3</option>
                            </select>
                        </label>
                    </li>
                    <li class="form__item">
                        <label class="form__item-wrapper">Случайные препятствия
                            <input class="form__item-entry" type="checkbox" name="random_obst" checked>
                        </label>
                    </li>
                </ul>
                <div class="main__buttons">
                    <a class="btn main__btn" href="<?= PATH ?>">Назад</a>
                    <button class="btn main__btn main__btn--primary" type="submit" name="start_game">Играть</button>
                </div>
            </form>
        </div>
    </div>
</section>

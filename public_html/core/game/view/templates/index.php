<section class="main">
    <div class="container">
        <div class="main__inner">
            <h1 class="title main__title">Чешский ЛИС!</h1>
            <div class="main__subtitle">
                <p class="main__subtitle-text">Обыграй хитрого лисенка в игре, где надо дойти до финишной черты</p>
                <p class="main__subtitle-text">Возводи преграды, стремись дойти до финиша быстрее этого прыткого
                    гаденыша!</p>
            </div>
            <div class="main__buttons">
                <a class="btn main__btn" href="#">Правила</a>
                <?php if ($this->matchID): ?>
                    <a class="btn main__btn main__btn--primary" href="<?=PATH?>play">Продолжить игру</a>
                <?php endif; ?>
                <a class="btn main__btn main__btn--primary" href="<?=PATH?>new">Новая игра</a>

            </div>
        </div>
    </div>
</section>
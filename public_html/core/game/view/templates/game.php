<section class="game">
    <div class="container">
        <div class="game__inner">
            <h1 class="title game__title">
                <?/*=$this->gameManager->getPlayerNames(' vs ')*/?>
                Игрок-1 vs Игрок-2
            </h1>
            <div class="game__board">
                <table class="game__field">
                    <?php for ($y = 0; $y < $sizey + ($sizey - 1); $y++): ?>
                        <?php if ($y % 2 === 0): ?>
                            <tr class="game__field-row">
                            <?php for ($x = 0; $x < $sizex + ($sizex - 1); $x++): ?>
                                <td class="game__field-cell">
                                    <?php if ($x % 2 === 0): ?>
                                        <div class="game__field-cell-tile"></div>
                                    <?php else: ?>
                                        <div class="game__field-cell-border game__field-cell-border--vertical"></div>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                            </tr>
                        <?php else: ?>
                            <tr class="game__field-row-border">
                                <?php for ($x = 0; $x < $sizex + ($sizex - 1); $x++): ?>
                                    <td class="game__field-cell">
                                        <?php if ($x % 2 === 0): ?>
                                            <div class="game__field-cell-border game__field-cell-border--horizontal"></div>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endif; ?>

                    <?php endfor; ?>
                </table>
                <div class="game__panel">
                    Игровая панелька
                </div>
            </div>
            <div class="game__buttons">
                <button class="btn game__btn" name="btn-1">Кнопка</button>
                <button class="btn game__btn" name="btn-1">Кнопка</button>
                <button class="btn game__btn" name="btn-1">Кнопка</button>
            </div>
        </div>
    </div>
</section>

<section class="game">
    <div class="container">
        <div class="game__inner">
            <h1 class="title game__title">
                <?= $playerNames ?>
            </h1>
            <div class="game__board">
                <table class="game__field">
                    <?php /*!!!пояснения!!!
                         ячейки начинаются с 1 до `размер` + (`размер - 1`) + 1
                         `размер`     - размер поля по одной из осей
                         `размер - 1` - кол-во ячеек барьеров
                         `+ 1`        - граница увеличена, так как начинается счет с 1
                         x, y - направляющие переменные для цикла,
                         x_pos, y_pos - игровые координаты для ячеек

                         проверка на нечетность для опредеения, куда ставить ячейку, а куда барьер

                         */ ?>
                    <?php for ($y = 1, $y_pos = 1; $y < $size_y + ($size_y - 1) + 1; $y++): ?>

                        <?php if ($y % 2 !== 0): ?>
                            <tr class="game__field-row">
                                <?php for ($x = 1, $x_pos = 1; $x < $size_x + ($size_x - 1) + 1; $x++): ?>
                                    <td class="game__field-cell">
                                        <?php if ($x % 2 !== 0): ?>
                                            <div class="game__field-cell-tile"
                                                 id="<?= join('-', ['tile', $x_pos++, $y_pos]) ?>">
                                            </div>
                                        <?php else: ?>
                                            <div class="game__field-cell-border game__field-cell-border--vertical"
                                                 id="<?= join('-', ['obst', $x_pos - 1, $y_pos, $x_pos, $y_pos]) ?>"></div>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php else: ?>
                            <tr class="game__field-row-border">
                                <?php for ($x = 1, $x_pos = 1, $y_pos++; $x < $size_x + ($size_x - 1) + 1; $x++): ?>
                                    <td class="game__field-cell">
                                        <?php if ($x % 2 !== 0): ?>
                                            <div class="game__field-cell-border game__field-cell-border--horizontal"
                                                 id="<?= join('-', ['obst', $x_pos++, $y_pos - 1, $x_pos - 1, $y_pos]) ?>"></div>
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
                <a class="btn game__btn" href="<?= PATH ?>" id="end_game">Завершить игру</a>
            </div>
        </div>
    </div>
</section>

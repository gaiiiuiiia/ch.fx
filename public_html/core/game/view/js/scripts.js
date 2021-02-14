
window.onload = () => {

    // событие на кнопку "завершение игры"
    document.querySelector('#end_game').onclick = function (e) {

        e.preventDefault();

        Ajax( {
            type: 'post',
            data: {
                ajax: 'endGame',
            }
        } ).then( (res) => {
            window.location.replace(res);
        } );

    };

    // если показано поле, загрузить информацию игры
    if (document.querySelector('.game__field')) {
        showGame();
        setClickOnPlayerListener();
    }

};

function showGame() {

    Ajax({
        type: 'post',
        data: {
            ajax:'getGameData',
        },
    }).then( (result) => {

        try{
            let gameData = JSON.parse(result);

            console.log(gameData);

            showPlayers(gameData['players']);
            showObstacles(gameData['obstacles']);

        }
        catch (e) {
            alert('Внутрення ошибка!');
            console.log('Ошибка - ' + e + '; Результат - ' + result);
            return false;
        }

    } );

}

function setClickOnPlayerListener() {

    document.querySelector('#p1').onclick = (e) => {

        getPossibleMoves();

    }
}

function getPossibleMoves() {

}

function showPlayers(data) {

    if (typeof data !== 'undefined' && data instanceof Array) {

        for (let player of data) {

            if (player.hasOwnProperty('position')) {

                let playerPosition = player['position'];

                let fieldTile = document.getElementById(`tile-${playerPosition['x']}-${playerPosition['y']}`);

                let playerDiv = document.createElement('div');
                playerDiv.classList.add('player');
                playerDiv.innerHTML = player['name'];
                fieldTile.append(playerDiv);
            }
        }
    }
}

function showObstacles(data) {

    console.log('data is ',  data);

    if (typeof data !== 'undefined' && data instanceof Array) {

        for (let obstacle of data) {

            // ставлю случайный цвет препятствию, хотя в стилях они все красные
            // для отладки
            let color = [
                getRandomInt(0, 256),
                getRandomInt(0, 256),
                getRandomInt(0, 256),
            ].join(',');

            for (let part of obstacle) {

                let obst = document.getElementById(
                    `obst-${part['fromx']}-${part['fromy']}-${part['tox']}-${part['toy']}`);

                obst.classList.add('game__field-cell-border--active');
                obst.style.backgroundColor = `rgb(${color})`;

            }

        }

    }

    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min)) + min; //Максимум не включается, минимум включается
    }

}
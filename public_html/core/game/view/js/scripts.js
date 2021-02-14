
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
            alert('Внутрення ошибка! Пришел не правильный JSON');
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

    console.log('data is ',  data);

    for (let player of data) {

        if (player.hasOwnProperty('position')) {

            let playerPosition = player['position'];
            console.log(playerPosition);

            let fieldTile = document.getElementById(`tile-${playerPosition['x']}-${playerPosition['y']}`);

            let playerDiv = document.createElement('div');
            playerDiv.classList.add('player');
            playerDiv.innerHTML = player['name'];
            fieldTile.append(playerDiv);

        }

    }

}

function showObstacles() {

}
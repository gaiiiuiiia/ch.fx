window.onload = () => {

    // событие на кнопку "завершение игры"
    if (document.querySelector('#end_game')) {
        document.querySelector('#end_game').onclick = function (e) {

            e.preventDefault();

            Ajax({
                type: 'post',
                data: {
                    ajax: 'endGame',
                }
            }).then((res) => {
                window.location.replace(res);
            });

        };
    }

    // если показано поле, загрузить информацию игры
    if (document.querySelector('.game__field')) {
        processGame();
    }

};

const playerCache = {
    name: null,
    possibleMoves: null,
    possibleObstacles: null,
};

function processGame() {
    Ajax({
        type: 'post',
        data: {
            ajax: 'getGameData',
        },
    }).then((result) => {
        try {
            let gameData = JSON.parse(result);
            console.log(gameData);

            showPlayers(gameData['players']);
            showObstacles(gameData['map']['obstacles']);
            setClickOnTileListener();
            setClickOnPlayerListener();

        } catch (e) {
            alert('Внутрення ошибка!');
            console.log('Ошибка - ' + e + '; Результат - ' + result);
            return false;
        }
    });
}

function setClickOnTileListener() {

    let tiles = document.getElementsByClassName('game__field-cell-tile');

    for (let tile of [...tiles]) {
        tile.onclick = function () {
            if (this.classList.contains('standby-click')) {

                let coords = this.id.split('-').slice(1);
                let data = {type: 'move'};
                data['x'] = coords[0];
                data['y'] = coords[1];
                processMove(data);

            } else {
                hidePossibleMoves();
                hidePossibleObstacles();
            }
        };
    }
}

function setClickOnPlayerListener() {

    for (let player of document.getElementsByClassName('player')) {
        player.onclick = function (e) {
            e.stopPropagation();
            let playerName = this.innerText;
            hidePossibleMoves();
            hidePossibleObstacles();

            if (playerCache.name !== playerName) {
                playerCache.name = playerName;
                getPossibleMoves(playerName).then(result => {
                    playerCache.possibleMoves = result;
                    showPossibleMoves(playerCache.possibleMoves);
                });
            } else {
                showPossibleMoves(playerCache.possibleMoves);
            }

            if (playerName === PLAYER_NAME) {
                if (!playerCache.possibleObstacles) {
                    getPossibleObstacles().then(result => {
                        playerCache.possibleObstacles = result;
                        showPossibleObstacles();
                    });
                } else {
                    showPossibleObstacles();
                }
            }
        }
    }
}

function processMove(data) {

    hidePossibleMoves();
    hidePossibleObstacles();

    Ajax({
        type: 'post',
        data: {
            ajax: 'makeMove',
            name: PLAYER_NAME,
            moveData: JSON.stringify(data),
        },
    }).then((result) => {
        result = JSON.parse(result);
        response(result);
    }).catch();
}

function response(data) {
    if (data['status'] === 'ok') {
        showPlayers(data['players']);
        showObstacles(data['map']['obstacles']);
        nextPlayerMove();
    } else {
        // wrong move !!!
        alert('Wrong move. doing nothing');
    }
}

function nextPlayerMove() {

}

function getPossibleMoves(playerName) {

    return new Promise((resolve, reject) => {
        Ajax({
            type: 'post',
            data: {
                ajax: 'getPossibleMoves',
                name: playerName,
            },
        }).then((result) => {
            let possibleMoves = [];
            for (let point of JSON.parse(result)) {
                possibleMoves.push(document.getElementById(`tile-${point['x']}-${point['y']}`));
            }
            resolve(possibleMoves);
        }).catch();
    });
}

function getPossibleObstacles() {

    return new Promise((resolve, reject) => {
        Ajax({
            type: 'post',
            data: {
                ajax: 'getPossibleObstacles',
            },
        }).then((result) => {

            let fieldObstacles = [];
            for (let obstacle of JSON.parse(result)) {
                let fieldObst = [];
                for (let part of obstacle) {
                    let from = JSON.parse(part['from']);
                    let to = JSON.parse(part['to']);

                    fieldObst.push(document.getElementById(`obst-${from['x']}-${from['y']}-${to['x']}-${to['y']}`));
                }
                fieldObstacles.push(fieldObst);
            }
            resolve(fieldObstacles);
        }).catch();
    });
}

function showPossibleMoves(moves) {

    [...moves].forEach(tile => {
        // если это игрок, то по плитке можно кликнуть
        if (playerCache.name === PLAYER_NAME) {
            tile.classList.add('standby-click');
        }

        tile.classList.add('tile--highlighted');
    });
}

function showPossibleObstacles() {

    if (playerCache.possibleObstacles !== 'undefined' && playerCache.possibleObstacles) {
        for (let i = 0; i < playerCache.possibleObstacles.length; i++) {

            if (!('eventListeners' in playerCache.possibleObstacles[i])
                || playerCache.possibleObstacles[i]['eventListeners'] === 'undefined') {

                playerCache.possibleObstacles[i]['eventListeners'] = {
                    _mousedown: obstacleMouseClickWrapper(playerCache.possibleObstacles[i]),
                    _mouseover: obstacleMouseOverWrapper(playerCache.possibleObstacles[i][0], playerCache.possibleObstacles[i]),
                    _mouseleave: obstacleMouseLeaveWrapper(playerCache.possibleObstacles[i][0], playerCache.possibleObstacles[i]),
                }
            }

            playerCache.possibleObstacles[i][0].addEventListener('mousedown', playerCache.possibleObstacles[i]['eventListeners']._mousedown);
            playerCache.possibleObstacles[i][0].addEventListener('mouseover', playerCache.possibleObstacles[i]['eventListeners']._mouseover);
            playerCache.possibleObstacles[i][0].addEventListener('mouseleave', playerCache.possibleObstacles[i]['eventListeners']._mouseleave);
        }
    }
}

function showPlayers(data) {

    if (typeof data !== 'undefined') {

        for (let player of data) {

            if (player.hasOwnProperty('position')) {

                let playerPosition = JSON.parse(player['position']);

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

    if (typeof data !== 'undefined') {

        data = JSON.parse(data);

        for (let obstacle of data) {

            // для отладки
            // ставлю случайный цвет препятствию, хотя в стилях они все красные
            let color = [
                getRandomInt(0, 200),
                getRandomInt(0, 100),
                getRandomInt(0, 256),
            ].join(',');

            for (let part of obstacle) {

                let from = JSON.parse(part['from']);
                let to = JSON.parse(part['to']);

                let obst = document.getElementById(
                    `obst-${from['x']}-${from['y']}-${to['x']}-${to['y']}`);

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

function hidePossibleMoves() {
    if (playerCache.possibleMoves !== 'undefined' && playerCache.possibleMoves) {
        for (let tile of [...playerCache.possibleMoves]) {
            if (tile.classList.contains('tile--highlighted')) {
                tile.classList.remove('tile--highlighted', 'standby-click');
            }
        }
    }
}

function hidePossibleObstacles() {

    if (playerCache.possibleObstacles !== 'undefined' && playerCache.possibleObstacles) {
        for (let obstacle of [...playerCache.possibleObstacles]) {
            if ('eventListeners' in obstacle && obstacle['eventListeners'] !== 'undefined') {
                obstacle[0].removeEventListener('mousedown', obstacle['eventListeners']._mousedown);
                obstacle[0].removeEventListener('mouseover', obstacle['eventListeners']._mouseover);
                obstacle[0].removeEventListener('mouseleave', obstacle['eventListeners']._mouseleave);
            }
        }
    }
}

const obstacleMouseClickWrapper = function (obstacle) {
    return function () {

        let data = {type: 'obstacle'};

        for (let i = 0; i < obstacle.length; i++) {
            let coordinates = obstacle[i].id.split('-').slice(1);
            data[`obst${i + 1}`] = {
                'fromx': coordinates[0],
                'fromy': coordinates[1],
                'tox': coordinates[2],
                'toy': coordinates[3],
            };
        }

        processMove(data);
    };
};

const obstacleMouseOverWrapper = function (partObstacle, obstacle) {
    return function () {
        partObstacle.classList.add('standby-click');
        obstacle.forEach((part) => {
            part.classList.add('border--highlighted');
        });
    };
};

const obstacleMouseLeaveWrapper = function (partObstacle, obstacle) {
    return function () {
        partObstacle.classList.remove('standby-click');
        obstacle.forEach((part) => {
            part.classList.remove('border--highlighted');
        });
    };
};

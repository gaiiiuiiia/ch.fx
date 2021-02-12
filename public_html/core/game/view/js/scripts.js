
window.onload = () => {

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

    if (document.querySelector('.game__field')) {

        let gameData = getGameData();

        console.log(gameData);

        //showPlayers();
        //showObstacles();
        //setClickOnPlayerListener();
    }

};

function getGameData() {

    Ajax({
        type: 'post',
        data: {
            ajax:'getGameData',
        },
    }).then( (result) => {

        try{
            console.log('res - ', result);
            return JSON.parse(result);
        }
        catch (e) {
            alert('Внутрення ошибка! Пришел не правильный JSON');
            console.log('Ошибка - ' + e + '; Результат - ' + result);
        }

    } ).catch( (result) => {

    } );

}

function setClickOnPlayerListener() {

    document.querySelector('#p1').onclick = (e) => {

        getPossibleMoves();

    }
}

function getPossibleMoves() {

    return;

    Ajax({
        data: {
            ajax: 'getPossibleMoves'
        },
    }).
    then( (result) => {
        // write some code
    } ).
    catch( (result) => {} );

}

function showPlayers() {

    Ajax({
        type: 'post',
        data: {
            ajax:'getPlayers',
        },
    }).then( (result) => {

        try{
            result = JSON.parse(result);

            console.log(result);
        }
        catch (e) {
            alert('Внутрення ошибка!');
        }

    } ).catch( (result) => {

    } );

}

function showObstacles() {

}
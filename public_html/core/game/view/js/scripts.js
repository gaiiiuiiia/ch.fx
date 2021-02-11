
window.onload = () => {
    showPlayers();
    showObstacles();
    setClickOnPlayerListener();
};


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
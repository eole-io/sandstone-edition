$(function () {
    bindRestApiButtons();
    bindChat();
    initUserFeelingIndicator();
});

function setIndicator(itemId, level, text) {
    var $item = $('#indicator-'+itemId);

    $item
        .removeClass('panel-success')
        .removeClass('panel-info')
        .removeClass('panel-warning')
        .removeClass('panel-danger')
        .removeClass('panel-default')
        .addClass('panel-'+level)
    ;

    if (text) {
        $item.find('.panel-body').html(text);
    }
}

function addToChat(text) {
    var $chatOutput = $('#chat-output');

    $chatOutput
        .append('<samp>'+text+'</samp><br />')
        .scrollTop($chatOutput[0].scrollHeight)
    ;
}

function bindRestApiButtons() {
    var apiUrl = Environment.restApiUrl;

    $('#api-hello').click(function () {
        $.get(apiUrl+'/hello').then(console.log);
    });

    $('#api-hello-sandstone').click(function () {
        $.get(apiUrl+'/hello/sandstone').then(console.log);
    });
}

function sendChatMessage(message) {
    websocketSession.publish('chat', message);
}

function bindChat() {
    $('#chat-form').submit(function (e) {
        e.preventDefault();

        var message = $('#chat-input').val();

        if (0 === message.length) {
            return false;
        }

        $('#chat-input').val('');

        sendChatMessage(message);
    });
}

function initUserFeelingIndicator() {
    guessUserFeeling().then(function (feel) {
        setIndicator('user-feeling', feel.level, 'You feel '+feel.text);
    });
}

function guessUserFeeling() {
    return new Promise(function (resolve) {
        setTimeout(function () {
            var hour = new Date().getHours();

            if (hour >= 3 && hour <= 8) {
                resolve({text: 'tired... <span class="glyphicon glyphicon-bed" aria-hidden="true"></span>', level: 'danger'});
                return;
            }

            if (12 === hour || 19 === hour) {
                resolve({text: 'hungry ! <span class="glyphicon glyphicon-apple" aria-hidden="true"></span>', level: 'danger'});
                return;
            }

            resolve({text: 'happy :)', level: 'success'});
        }, 2000);
    });
}

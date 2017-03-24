var websocketSession = null;

/**
 * Called when connected to chat server.
 *
 * @param {wampSession} session Object provided by autobahn to subscribe/publish/unsubscribe.
 */
function onSessionOpen(session) {
    websocketSession = session;
    var event = new CustomEvent('websocketSessionReady', {
        detail: {
            websocketSession: session
        },
        bubbles: true,
        cancelable: true
    });
    dispatchEvent(event);

    setIndicator('websocket-connection', 'success', 'Connected to <code>'+session._wsuri+'</code>');

    // Subscribe to 'chat/general' topic to display messages on chat output
    session.subscribe('chat', function (topic, event) {
        setIndicator('chat-topic', 'success', 'Subscribed to chat topic.');

        addToChat(event.message);
    });

    // Publish a message to 'chat/general' topic
    session.publish('chat', 'Hello friend !');
}

/**
 * Called on error.
 *
 * @param {Integer} code
 * @param {String} reason
 * @param {String} detail
 */
function onError(code, reason, detail) {
    console.warn('error', code, reason, detail);

    setIndicator('websocket-connection', 'danger', [code, reason, detail].join(' ; '));
    setIndicator('chat-topic', 'danger', 'Needs websocket connection first');
    setIndicator('push', 'danger', 'Needs websocket connection first');
}

// Connect to chat server
ab.connect(Environment.websocketServer, onSessionOpen, onError);

// Testing Rest Api
$.get(Environment.restApiUrl+'/hello')
    .then(function (response, text, metadata) {
        setIndicator('rest-api', 'success', 'Rest Api answered with <code>'+metadata.status+' '+metadata.statusText+'<br />'+metadata.responseText+'</code>');
    })
    .fail(function () {
        console.warn('Rest Api fail', arguments);
        setIndicator('rest-api', 'danger', 'Error when trying to call <code>GET /api/hello</code>.');
    })
;

// Testing Push notifications
addEventListener('websocketSessionReady', function (e) {
    e.detail.websocketSession.subscribe('chat', function (topic, event) {
        if (-1 !== event.message.indexOf('push-1-2-1-2-test')) {
            setIndicator('push', 'success', 'Push notification received.');
        }
    });

    setIndicator('push', 'warning', 'Requesting Rest Api and waiting for push notification...');
    $.get(Environment.restApiUrl+'/hello/push-1-2-1-2-test');
});

var websocketSession = null;

/**
 * Called when connected to chat server.
 *
 * @param {wampSession} session Object provided by autobahn to subscribe/publish/unsubscribe.
 */
function onSessionOpen(session) {
    websocketSession = session;
    var event = new CustomEvent('websocketSessionReady', {
        websocketSession: session,
        bubbles: true,
        cancelable: true
    });
    dispatchEvent(event);

    setIndicator('websocket-connection', 'success', 'Connected to <code>'+session._wsuri+'</code>');

    // Subscribe to 'chat/general' topic to display messages on chat output
    session.subscribe('chat', function (topic, event) {
        console.log('message received', topic, event);

        setIndicator('chat-topic', 'success', 'Subscribed to chat topic.');

        if (event.message) {
            addToChat(event.message);
        }
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
    e.websocketSession.subscribe('chat', function (topic, event) {
        if (event.hello && 'push-1-2-1-2-test' === event.hello) {
            setIndicator('push', 'success', 'Push notification received.');
        }
    });
});

$.get(Environment.restApiUrl+'/hello/push-1-2-1-2-test');

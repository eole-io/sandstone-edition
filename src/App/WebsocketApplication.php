<?php

namespace App;

use App\Topic\ChatTopic;

class WebsocketApplication extends Application
{
    /**
     * Initialize Sandstone application.
     *
     * {@Inheritdoc}
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->topic('chat', function ($topicPattern) {
            return new ChatTopic($topicPattern);
        });
    }
}

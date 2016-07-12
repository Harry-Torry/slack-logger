<?php
    require 'vendor/autoload.php';
    use Illuminate\Database\Eloquent\Model as Model;
    use HarryTorry\SlacKeep\Message;

    \HarryTorry\SlacKeep\bootstrap::init();

    $loop = React\EventLoop\Factory::create();

    $client = new Slack\RealTimeClient($loop);
    $client->setToken(getenv('SLACK_TOKEN'));

    $client->on('message', function (Slack\Payload $data) use ($client) {
        echo sprintf(
            'Message~ %s: %s%s',
            $data['user'],
            $data['text'],
            PHP_EOL
        );

        (new Message($data->getData()))->save();
    });

    $client->connect()->then(function () {
        echo "Connected!\n";
    });

    $loop->run();

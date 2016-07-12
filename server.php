<?php
    require 'vendor/autoload.php';
    use Illuminate\Database\Capsule\Manager as Capsule;
    use Illuminate\Database\Eloquent\Model as Model;

    class Message extends Model {
        protected $table = 'messages';
        protected $fillable = ['reply_to', 'type', 'channel', 'user', 'text', 'ts'];
    }

    setupDatabase();
    migrationUp();
//    migrationDown();

    $loop = React\EventLoop\Factory::create();

    $client = new Slack\RealTimeClient($loop);
    $client->setToken('');

    $client->on('message', function (Slack\Payload $data) use ($client) {
        echo sprintf(
            'Message~ %s: %s%s',
            $data['user'],
            $data['text'],
            PHP_EOL
        );

        (new Message($data->getData()))->save();
        $client
            ->getDMById('@HKAN ID goes here')
            ->then(function (\Slack\Channel $channel) use ($client, $data) {
                $message = sprintf(
                  '%s %s: %s',
                    'new message from:',
                    $data->user,
                    $data->text
                );

                $client->send($message, $channel);
            });
    });

    $client->connect()->then(function () {
        echo "Connected!\n";
    });

    $loop->run();

    function setupDatabase() {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => '192.168.33.7',
            'database'  => 'personal_slack',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    function migrationUp() {
        Capsule::schema()->create('messages', function($table)
        {
            $table->increments('id');
            $table->integer('reply_to');
            $table->string('type');
            $table->string('channel');
            $table->string('user');
            $table->string('text', 4096);
            $table->string('ts');
            $table->timestamps();
        });
        die('Tables created, comment out `migrationUp();`' . PHP_EOL);
    }

    function migrationDown() {
        Capsule::schema()->drop('messages');
        die('Tables deleted, comment out `migrationDown();`' . PHP_EOL);
    }


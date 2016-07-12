<?php

namespace HarryTorry\SlacKeep;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

class bootstrap {

    /**
     * bootstrap constructor.
     */
    public static function init()
    {
        $bootstrap = new bootstrap();
        $bootstrap->loadEnvironment();
        $bootstrap->setupDatabaseConnection();
        $bootstrap->handleCommandLine();
    }

    private function handleCommandLine() {
        global $argc, $argv;

        $validOptions  = array(
            "migrate",
            "migrate:reset",
        );

        if ($argc == 1) { // if nothing has been added
            return;
        }

        array_shift($argv); // remove the script

        if ($argv[0] == 'migrate') {
            $this->migrationUp();
        }

        if ($argv[0] == 'migrate:reset') {
            $this->migrationDown();
        }
    }

    private function setupDatabaseConnection() {
        $capsule = new Capsule();
        
        $capsule->addConnection([
            'driver'    => $this->env('DB_DRIVER', 'mysql'),
            'host'      => $this->env('DB_HOST'),
            'database'  => $this->env('DB_DATABASE'),
            'username'  => $this->env('DB_USERNAME'),
            'password'  => $this->env('DB_PASSWORD'),
            'charset'   => $this->env('DB_CHARSET', 'utf8'),
            'collation' => $this->env('DB_COLLATION', 'utf8_unicode_ci'),
            'prefix'    => $this->env('DB_PREFIX', ''),
        ]);
        
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    private function migrationUp() {
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
        die('Migrations created.' . PHP_EOL);
    }

    private function migrationDown() {
        Capsule::schema()->drop('messages');
        die('Migrations reset.' . PHP_EOL);
    }

    private function loadEnvironment()
    {
        $requiredFields = [
            'DB_HOST',
            'DB_DATABASE',
            'DB_USER',
            'DB_PASS',
            'SLACK_TOKEN'
        ];

        $dotenv = new Dotenv(realpath(__DIR__ . '/../../../'));
        $dotenv->load();
        $dotenv->required($requiredFields)->notEmpty();
    }

    private function env($name, $default = null)
    {
        if ($env = getenv($name)) {
            return $env;
        }
        return $default;
    }
}


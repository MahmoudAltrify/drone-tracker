<?php

namespace App\Console\Commands;

use App\Models\DangerousDrone;
use App\Models\Drone;
use App\Services\TelemetryProcessorService;
use App\Strategies\AltitudeDangerStrategy;
use App\Strategies\SpeedDangerStrategy;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpMqtt\Client\{ConnectionSettings, MqttClient};
use Illuminate\Support\Facades\Log;

class DroneListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to MQTT for drone telemetry';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $server = config('services.mqtt.connection');
        $port = config('services.mqtt.port');
        $clientId = 'laravel-client-' . uniqid();
        $connectionSettings = (new ConnectionSettings)
            ->setUsername(null)
            ->setPassword(null);

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($connectionSettings, true);
        Log::info('Connected');
        $mqtt->subscribe('device/+/osd', function (string $topic, string $message) {
            $this->info("[$topic] $message");

            $payload = json_decode($message, true);
            if (!$payload) {
                Log::warning("Invalid JSON from topic: $topic");
                return;
            }

            app(TelemetryProcessorService::class)->process($topic, $payload);
        }, 0);

        $mqtt->loop(true);
        $mqtt->disconnect();
    }
}

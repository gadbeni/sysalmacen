<?php

namespace App\Providers;

use App\Actions\ToggleUserStatusAction;
use Illuminate\Support\ServiceProvider;
// use TCG\Voyager\Voyager;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Pagination\Paginator;

use App\FormFields\DireccionAdministrativaFormField;
use App\FormFields\SucursalFormField;
use App\Filesystem\ResilientAwsS3V3Adapter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Aws\S3\S3Client;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter as S3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter as AwsS3PortableVisibilityConverter;
use League\Flysystem\Visibility;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Voyager::addFormField(DireccionAdministrativaFormField::class);
        Voyager::addFormField(SucursalFormField::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voyager::addAction(ToggleUserStatusAction::class);

        // Disco S3 tolerante a fallos de verificación de existencia (DigitalOcean Spaces
        // responde 403 en objetos inexistentes y Flysystem lanza excepción). Ver ResilientAwsS3V3Adapter.
        Storage::extend('s3', function ($app, $config) {
            $s3Config = $config + ['version' => 'latest'];

            if (! empty($s3Config['key']) && ! empty($s3Config['secret'])) {
                $s3Config['credentials'] = Arr::only($s3Config, ['key', 'secret']);
            }
            if (! empty($s3Config['token'])) {
                $s3Config['credentials']['token'] = $s3Config['token'];
            }
            $s3Config = Arr::except($s3Config, ['token']);

            $root = (string) ($s3Config['root'] ?? '');
            $visibility = new AwsS3PortableVisibilityConverter(
                $config['visibility'] ?? Visibility::PUBLIC
            );
            $streamReads = $s3Config['stream_reads'] ?? false;

            $client = new S3Client($s3Config);
            $adapter = new S3Adapter(
                $client, $s3Config['bucket'], $root, $visibility, null, $config['options'] ?? [], $streamReads
            );

            return new ResilientAwsS3V3Adapter(
                new Flysystem($adapter, $s3Config), $adapter, $s3Config, $client
            );
        });

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
        
        Paginator::useBootstrap();
    }
}

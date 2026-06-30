<?php

namespace App\Filesystem;

use Illuminate\Filesystem\AwsS3V3Adapter;
use Throwable;

/**
 * Adaptador S3 endurecido para DigitalOcean Spaces en entornos Windows.
 *
 * Resuelve dos problemas:
 *
 * 1) Normalización de separadores: Voyager construye las rutas de subida con
 *    DIRECTORY_SEPARATOR (en Windows '\\'), generando keys como
 *    "settings\June2026\x.png". Eso produce URLs con %5C y objetos inaccesibles.
 *    Aquí forzamos '/' en toda ruta antes de delegar al adaptador real, de modo
 *    que la key en S3 y la URL pública siempre usen '/'.
 *
 * 2) Verificación de existencia: Spaces responde 403 (en vez de 404) en objetos
 *    inexistentes y Flysystem lanza UnableToCheck(File|Directory)Existence,
 *    rompiendo vistas de Voyager. Tratamos ese error como "no existe" (false).
 */
class ResilientAwsS3V3Adapter extends AwsS3V3Adapter
{
    protected function norm($path)
    {
        return is_string($path) ? str_replace('\\', '/', $path) : $path;
    }

    // --- Normalización de rutas ---

    public function put($path, $contents, $options = [])
    {
        return parent::put($this->norm($path), $contents, $options);
    }

    public function putFile($path, $file = null, $options = [])
    {
        return parent::putFile($this->norm($path), $file, $options);
    }

    public function putFileAs($path, $file, $name = null, $options = [])
    {
        return parent::putFileAs($this->norm($path), $file, $name, $options);
    }

    public function get($path)
    {
        return parent::get($this->norm($path));
    }

    public function readStream($path)
    {
        return parent::readStream($this->norm($path));
    }

    public function delete($paths)
    {
        $paths = is_array($paths) ? array_map([$this, 'norm'], $paths) : $this->norm($paths);

        return parent::delete($paths);
    }

    public function copy($from, $to)
    {
        return parent::copy($this->norm($from), $this->norm($to));
    }

    public function move($from, $to)
    {
        return parent::move($this->norm($from), $this->norm($to));
    }

    public function size($path)
    {
        return parent::size($this->norm($path));
    }

    public function lastModified($path)
    {
        return parent::lastModified($this->norm($path));
    }

    public function mimeType($path)
    {
        return parent::mimeType($this->norm($path));
    }

    public function getVisibility($path)
    {
        return parent::getVisibility($this->norm($path));
    }

    public function setVisibility($path, $visibility)
    {
        return parent::setVisibility($this->norm($path), $visibility);
    }

    public function url($path)
    {
        return parent::url($this->norm($path));
    }

    public function temporaryUrl($path, $expiration, array $options = [])
    {
        return parent::temporaryUrl($this->norm($path), $expiration, $options);
    }

    // --- Tolerancia a fallos de verificación de existencia ---

    public function exists($path)
    {
        try {
            return parent::exists($this->norm($path));
        } catch (Throwable $e) {
            return false;
        }
    }

    public function missing($path)
    {
        return ! $this->exists($path);
    }

    public function fileExists($path)
    {
        try {
            return parent::fileExists($this->norm($path));
        } catch (Throwable $e) {
            return false;
        }
    }

    public function fileMissing($path)
    {
        return ! $this->fileExists($path);
    }

    public function directoryExists($path)
    {
        try {
            return parent::directoryExists($this->norm($path));
        } catch (Throwable $e) {
            return false;
        }
    }

    public function directoryMissing($path)
    {
        return ! $this->directoryExists($path);
    }
}

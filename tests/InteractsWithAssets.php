<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

trait InteractsWithAssets
{
    /**
     * @param  array  $fileNames
     * @param  bool  $usePrefix
     * @return void
     */
    public function useMigrations(array $fileNames, bool $usePrefix = true): void
    {
        foreach ($fileNames as $fileName) {
            if ($usePrefix) {
                $fileName = $this->migrationPrefix($fileName);
            }

            copy(
                $this->assetsPath('migrations/'.$fileName.'.php'),
                $this->dynamicPath('migrations/'.$fileName.'.php')
            );
        }
    }

    /**
     * @param  string  $path
     * @return string
     */
    public function assetsPath(string $path = ''): string
    {
        return $this->resolvePath('assets', $path);
    }

    /**
     * @param  string  $path
     * @return string
     */
    public function dynamicPath(string $path = ''): string
    {
        return $this->resolvePath('assets/dynamic', $path);
    }

    /**
     * @param  string  $fileName
     * @return string
     */
    public function migrationPrefix(string $fileName): string
    {
        return '2020_01_01_000000_create_'.$fileName.'_table';
    }

    /**
     * @return void
     */
    protected function refreshDynamic(): void
    {
        $it = new \RecursiveDirectoryIterator($this->dynamicPath(), \FilesystemIterator::SKIP_DOTS);
        $it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($it as $obj) {
            if ($obj->isFile() && $obj->getBasename() !== '.gitignore') {
                unlink($obj->getPathname());
            }
        }
    }

    /**
     * @param  string  $folder
     * @param  string  $path
     * @return string
     */
    protected function resolvePath(string $folder, string $path): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.$folder.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

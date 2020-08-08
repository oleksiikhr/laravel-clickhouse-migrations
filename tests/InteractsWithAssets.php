<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

trait InteractsWithAssets
{
    /**
     * @param  array  $fileNames
     * @return void
     */
    public function useMigrations(array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
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

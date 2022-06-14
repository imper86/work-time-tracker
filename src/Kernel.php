<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $filesystem = new Filesystem();
        $absolutePath = \sprintf('%s/.work-time-tracker', $_SERVER['HOME']);

        if (!\file_exists($absolutePath)) {
            \mkdir($absolutePath, 0755);
        }

        $relativeDir = $filesystem->makePathRelative($absolutePath, $container->getParameter('kernel.project_dir'));

        $container->setParameter('sqlite_path', \sprintf('%sdata.db', $relativeDir));
    }
}

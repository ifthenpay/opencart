<?php

declare(strict_types=1);

namespace Ifthenpay\Utility;

class Mix {
   
    public function create(string $path): string
    {
        $manifestPath = DIR_SYSTEM .'library/ifthenpay/utility/assetversionlist.json';
        if (!file_exists($manifestPath)) {
            throw new \Exception('assetVersionList file not exist');
        }
        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (!array_key_exists($path, $manifest)) {
            throw new \Exception(
                "Unable to locate Mix file: {$path}. Please check your ".
                'webpack.mix.js output paths and try again.'
            );
        }
        return $manifest[$path];
    }
}

<?php

namespace Inovector\Mixpost\Actions;

use Inovector\Mixpost\Models\Service;

class UpdateOrCreateService
{
    public function __invoke(string $name, array $value): Service
    {
        return Service::query()->updateOrCreate(['name' => $name], [
            'credentials' => $value
        ]);
    }
}

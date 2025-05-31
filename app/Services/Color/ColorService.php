<?php

namespace App\Services\Color;

use App\Models\Color;
use App\Services\BaseService;

class ColorService extends BaseService
{

    public function createColor(array $data) {
        $color = new Color($data);
        if (!$color->save()) {
            throw new \Exception('Error creating product color');
        }
        return true;
    }
    public function updateColor(Color $color, array $data) {
        if (!$color->update($data)) {
            throw new \Exception('Error updating product color');
        }
        return true;
    }

    public function deleteColor(Color $color) {
        if (!$color->delete()) {
            throw new \Exception('Error deleting product color');
        }
        return true;
    }

}

<?php

namespace App\Models;

trait HasMIChI
{
    public function seedMIChI($MIChIService)
    {
        if ($this instanceof Dataset) {
            $array = $MIChIService->extractMichi($this);
            $michiDict = $array[0];
            $michiKeysMap = $array[1];
            foreach ($michiDict as $key => $value) {
                if (isset($michiKeysMap[$key])) {
                    $this->{$michiKeysMap[$key]} = $value;
                }
            }
            $this->save();
        }
    }
}

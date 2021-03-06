<?php

namespace App\Http\Controllers;

use App\Models\DomesticDeliveryItem;
use Illuminate\Http\Request;

class DomesticDeliveryItemController extends Controller
{
    public function destroy(DomesticDeliveryItem $domesticDeliveryItem)
    {
        $domesticDeliveryItem->delete();
        return ['message' => 'Data berhasil dihapus'];
    }
}

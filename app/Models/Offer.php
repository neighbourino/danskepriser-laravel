<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'quantity_unit' => 'array',
        'quantity_pieces' => 'array',
        'quantity_size' => 'array',
    ];


    private static function fetchOffers($query = null)
    {
        if (!$query) {
            return false;
        }
        $requestUrl = 'https://etilbudsavis.dk/api/squid/v2/offers/search?query=' . $query . '&r_lat=55.695497&r_lng=12.550145&r_radius=20000&r_locale=da_DK&limit=50&offset=0';

        $response = Http::get($requestUrl);

        $data = $response->json();

        foreach ($data as $offer) {

            Offer::create([
                'api_offer_id' => $offer['id'],
                'offer_heading' => $offer['heading'],
                'offer_description' => $offer['description'],
                'price' => $offer['pricing']['price'],
                'quantity_unit' => $offer['quantity']['unit'],
                'quantity_size' => $offer['quantity']['size'],
                'quantity_pieces' => $offer['quantity']['pieces'],
                'store' => $offer['dealer']['name'],
                'run_from' => Carbon::parse($offer['run_from'], 'UTC')->toDateTimeString(),
                'run_till' => Carbon::parse($offer['run_till'], 'UTC')->toDateTimeString(),
                'publish' => Carbon::parse($offer['publish'], 'UTC')->toDateTimeString(),
                'api_store_id' => $offer['dealer']['id'],
                'store_id' => null,
            ]);
        }

        return true;
    }
}

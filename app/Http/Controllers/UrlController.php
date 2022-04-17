<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\UrlClick;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class UrlController extends Controller
{
    //Get all orders
    public function createUrl(Request $request)
    {
        $check = Validator::make($request->all(), [
            'destination_url' => 'required|string',
            'alias' => 'nullable|string|max:15',
        ]);

        if ($check->fails()) {
            return response()->json(['status' => false, 'message' => $check->errors()->first()], 400);
        }

        $pattern = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:[a-zA-Z])|\d+\.\d+\.\d+\.\d+)/';

        if (!preg_match($pattern, $request->destination_url)) {
            return response()->json([
                'status' => false,
                'message' => "Invalid destination url."
            ], 400);
        }

        if ($request->alias) {
            if (strtolower($request->alias) == "url") {
                return response()->json([
                    'status' => false,
                    'message' => "This alias is not available."
                ], 400);
            }

            $checkAlias = Url::where('alias', $request->alias)->first();

            if ($checkAlias) {
                return response()->json([
                    'status' => false,
                    'message' => "This alias is already in use."
                ], 400);
            }
        } else {
            //Auto generate alias if user did not provide
            while (true) {
                $request->alias = substr(bin2hex(openssl_random_pseudo_bytes(5)), 0, 4);
                $checkAlias = Url::where('alias', $request->alias)->first();

                if ($checkAlias == null) {
                    break;
                }
            }
        }

        $url = Url::create([
            'destination_url' => $request->destination_url,
            'alias' => $request->alias,
            'title' => $this->getTitleFromUrl($request->destination_url)
        ]);

        return response()->json([
            'status' => true,
            'message' => "URL successfully shortened!",
            'url' => $url,
        ], 200);
    }

    // Update existing URL's title
    public function updateTitles(Request $request)
    {
        $urls = Url::get();

        foreach ($urls as $singleUrl) {
            $singleUrl->update(['title' => $this->getTitleFromUrl($singleUrl->destination_url)]);
        }

        return $urls;
    }

    public function getUrl(Request $request)
    {
        $check = Validator::make($request->all(), [
            'alias' => 'required|string|max:15',
        ]);

        if ($check->fails()) {
            return response()->json(['status' => false, 'message' => $check->errors()->first()], 400);
        }

        $url = Url::where('alias', $request->alias)->first();

        if ($url == null) {
            return response()->json([
                'status' => false,
                'message' => "Url does not exist."
            ], 404);
        }

        // Update clicks database

        $location = "Unknown";

        if (request()->ip() == '127.0.0.1') {
            $location = "Local testing env";
        }

        // if ($position = Location::get('115.135.70.141')) {
        if ($position = Location::get(request()->ip())) {
            // Successfully retrieved position.
            $location = ($position->regionName) . ', ' . ($position->countryName);
        }


        UrlClick::create([
            'url_alias' => $request->alias,
            'location' => $location,
        ]);

        return response()->json([
            'status' => true,
            'url' => $url,
        ], 200);
    }

    public function getUrls(Request $request)
    {
        $check = Validator::make($request->all(), [
            'aliases' => 'required|array',
        ]);

        if ($check->fails()) {
            return response()->json(['status' => false, 'message' => $check->errors()->first()], 400);
        }

        $urls = Url::whereIn('alias', $request->aliases)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'urls' => $urls,
        ], 200);
    }

    public function getUrlDetails(Request $request)
    {
        $check = Validator::make($request->all(), [
            'alias' => 'required|string|max:15',
        ]);

        if ($check->fails()) {
            return response()->json(['status' => false, 'message' => $check->errors()->first()], 400);
        }

        $urlClicks = UrlClick::where('url_alias', $request->alias)->get();

        return response()->json([
            'status' => true,
            'url_clicks' => $urlClicks,
        ], 200);
    }
}

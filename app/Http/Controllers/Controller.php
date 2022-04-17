<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getTitleFromUrl(String $url)
    {

        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        try {
            // Reduce timeout time so users don't have to wait too long
            $ctx = stream_context_create(['http' => ['timeout' => 5]]); // 5 seconds
            $html = file_get_contents($url, null, $ctx);

            $res = preg_match('/og:site_name(.*)content="(.*)"(.*)>/siU', $html, $title_matches);
            if ($res) {
                $title = preg_replace('/\s+/', ' ', $title_matches[2]);
                return trim($title);
            }

            $res = preg_match('/al:android:app_name(.*)content="(.*)"(.*)>/siU', $html, $title_matches);
            if ($res) {
                $title = preg_replace('/\s+/', ' ', $title_matches[2]);
                return trim($title);
            }

            $res = preg_match('/opensearchdescription(.*)title="(.*)"(.*)>/siU', $html, $title_matches);
            if ($res) {
                $title = preg_replace('/\s+/', ' ', $title_matches[2]);
                return trim($title);
            }

            $res = preg_match("/<title(.*)>(.*)<\/title>/siU", $html, $title_matches);
            if (!$res)
                return "Unknown Website";
            $title = preg_replace('/\s+/', ' ', $title_matches[2]);

            return trim($title);
        } catch (Exception $e) {
            return "Unknown Website";
        }
    }
}

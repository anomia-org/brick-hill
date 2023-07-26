<?php

namespace App\Helpers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Input;

use \Carbon\Carbon;
use \Request;
use DateTime;

use Parsedown;

use App\Models\Set\Set;

// applied to every parsed markdown
class MarkdownExtension extends Parsedown {
    protected function inlineUrl($exc) {
        return;
    }

    protected function inlineEmailTag($exc) {
        return;
    }

    protected function inlineUrlTag($exc) {
        return;
    }
}

// applied to markdown ran on non admin accounts
class Markdown extends MarkdownExtension {
    protected function inlineLink($exc) {
        return;
    }

    protected function inlineImage($exc) {
        return;
    }
}

// applied to markdown ran on admin accounts
class MarkdownAdmin extends MarkdownExtension {
    protected function inlineLink($exc) {
        $element = parent::inlineLink($exc);
        if (!isset($element))
            return null;
        return $element;
    }

    protected function inlineImage($exc) {
        $element = parent::inlineImage($exc);
        if (!isset($element))
            return null;
        $element['element']['attributes']['style'] = "max-width:100%;";
        return $element;
    }
}

class Helper {

    public static function numAbbr($number) {
        $abbrevs = array(6 => 'M+', 3 => 'K+', 0 => '');
        foreach($abbrevs as $exponent => $abbrev) {
            if($number >= pow(10, $exponent)) {
                $display_num = floor(($number / pow(10, $exponent)) * 10) / 10;
                $decimals = ($exponent >= 3 && round($display_num) < 100) ? 1 : 0;
                return number_format($display_num, $decimals) . $abbrev;
            }
        }
        return $number;
    }

    public static function time_elapsed_string($datetime, $truncateToNow = false) {
        if(is_null($datetime)) {
            return "Never";
        }
        $date = Carbon::createFromTimeStamp(strtotime($datetime));
        $oneMinute = Carbon::now()->subMinute();
        if($truncateToNow && $oneMinute <= $date) {
            return 'Now';
        }
        return $date->diffForHumans();
    }

    public static function bbcode_to_html($bbtext, $adminPower, $colorClass = ""){
        $allowedLinks = [
            'https://www.brick-hill.com',
            'https://brick-hill.com',
            'https://api.brick-hill.com',
            'https://teach.brick-hill.com',
            'https://blog.brick-hill.com',
            'https://wiki.brick-hill.com',
            'https://brick-hill.trade',
            'https://twitter.com',
            'https://www.youtube.com',
            'https://youtu.be',
            'https://www.twitch.tv',
            'https://gitlab.com'
        ];
        
        $widgets = [
            'set' => request()->getHost().'\/play\/([0-9]*)'
        ];

        $bbemotes = array(
            'B)' => '<span class="emote-sunglasses"></span>', // doesnt work because ? ? ? ? ?? ?
            ':)' => '<span class="emote-happy"></span>',
            '>:(' => '<span class="emote-angry"></span>',
            ':(' => '<span class="emote-sad"></span>',
            ':P' => '<span class="emote-tongue"></span>',
            ':\/' => '<span class="emote-displeased"></span>', // extra slash because regex hates it
            ':D' => '<span class="emote-grin"></span>',
            ':O' => '<span class="emote-surprised"></span>',
            ';;)' => '<span class="emote-wink2"></span>',
            ';)' => '<span class="emote-wink"></span>',
            '|)' => '<span class="emote-squint"></span>',
            ';D' => '<span class="emote-laugh"></span>',
            ';(' => '<span class="emote-cry"></span>',
            ':sleepy:' => '<span class="emote-sleep"></span>',
            ':saint:' => '<span class="emote-saint"></span>',
            ':coffee:' => '<span class="emote-coffee" style="padding-right:5px;"></span>',
            ':thumbsup:' => '<span class="emote-thumbsup" style="padding-right:5px;"></span>'
        );
            
        if ($adminPower > 0) { 
            $parsedown = new MarkdownAdmin();
            $parsedown->setSafeMode(true);

            $bbtext = $parsedown->line($bbtext);
        } else {
            $parsedown = new Markdown();
            $parsedown->setSafeMode(true);

            $bbtext = $parsedown->line($bbtext);
        }

        foreach($bbemotes as $match => $replace) {
            $first = $match[0];
            $match = e(str_replace("\\\/", "\/", preg_quote($match))); // encode the match so its the same input that is being read
            $bbtext = preg_replace("/(?!&#[0-9]{3};)(^|\B)($match)/i", $replace, $bbtext);
        }

        // makes brick-hill urls clickable
        // i havent tested this much and made it myself
        // except for the regex obviously im not that smart
        // this might be a very bad idea and able to be bugged horrible and make hyperlinked bad boi urls
        // ^ predicting magic
        preg_match_all('@((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)@', $bbtext, $filtered, PREG_OFFSET_CAPTURE);
        if(count($filtered) > 0) {
            $addedCount = 0;
            foreach($filtered[0] as $i => $arr) {
                $url = $arr[0];
                $offset = $arr[1];
                
                // add slashes to prevent https://brick-hill.company getting embedded
                $exact_links = preg_filter('/$/', '/', $allowedLinks);
                
                // check if the links contains https://url.tld/ or if the url = https://url.tld
                if(Helper::contains($url, $exact_links) || in_array($url, $allowedLinks)) {
                    $newUrl = '<a target="_blank" href="'.$url.'">'.$url.'</a>';
                    $bbtext = substr_replace($bbtext, $newUrl, $offset + $addedCount, strlen($url));
                    $addedCount += strlen($newUrl) - strlen($url);
                }
            }
        }
        
        return $bbtext;
    }


    public static function paginate($perPage, $current, $pageAmount, $count, $url = '') {
        $pages = [];

        for($i = 1; $i <= $count; $i++) {
            if($i <= $current + $pageAmount / 2 && $i >= $current - $pageAmount / 2) {
                $pages[] = $i;
            }
        }

        return [
            'pageCount' => $count,
            'current' => (int) $current,
            'next' => ($current + 1 <= $count) ? $current + 1 : false,
            'previous' => ($current - 1 > 0) ? $current - 1 : false,
            'pages' => $pages
        ];
    }

    public static function ifOver($pageData) {
        if($pageData['pageCount'] < $pageData['current'] && $pageData['pageCount'] != 0)
            return true;
        return false;
    }

    private static function contains($string, array $arr) {
        foreach($arr as $a) {
            if(substr($string, 0, strlen($a)) === $a) return true;
        }
        return false;
    }

}

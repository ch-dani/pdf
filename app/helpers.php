<?php
function route($name, $parameters = [], $absolute = true)
{
    global $lang_code, $aw_languages;
    $lcode = $lang_code;
    if (isset($parameters[0]) and in_array($parameters[0], $aw_languages)) {
        $lcode = $parameters[0];
    }


    if (isset($parameters['lang'])) {
        $lcode = $parameters['lang'];
    }

    if (request()->path() !== 'password/send-email') {
        if ($lcode == 'en') {
            $lcode = "";
            $parameters = array_values($parameters);
            return str_replace("/en/", "/", app('url')->route($name, $parameters, $absolute));
        } else {
            $params = ['lang' => $lcode] + $parameters;
            return app('url')->route($name, $params, $absolute);
        }
    }
}

function lurl($url)
{
    global $lang_code;
    if ($lang_code == 'en') {
        return url($url);
    }
    $url = ltrim($url, "/");

    return url("/$lang_code/$url");
}

function t($text = "")
{
    global $lang_id;
    $text = trim($text);

    $new_obj = \App\LanguageConstatns::where("key", $text)->first();

    if (!$new_obj) {
        $new_obj = [];
        foreach (\App\Language::all() as $l) {
            if ($l->id == 1) {
                $new_obj[$l->id] = $text;
            } else {
                $new_obj[$l->id] = "";
            }
        }

        \App\LanguageConstatns::create([
            "key" => $text,
            "translate" => json_encode($new_obj)
        ]);
        return $text;
    } else {
        $new_obj = json_decode($new_obj['translate'], 1);
//        $new_obj = json_decode($new_obj->translate, 1);
        $ret = ($new_obj && isset($new_obj[$lang_id])) ? $new_obj[$lang_id] : $text;
        if (!$ret) {
            return $text;
        }

        return $ret;
    }

    return $text;
}


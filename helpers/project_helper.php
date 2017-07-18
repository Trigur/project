<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (! function_exists('ajaxCheck')) {
    function ajaxCheck()
    {
        $ci = & get_instance();
        return ($ci->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest');
    }
}

if (! function_exists('getPage')) {
    function getPage($pageId)
    {
        $ci = &get_instance();

        $language = MY_Controller::getCurrentLanguage();

        $ci->db->limit(1);
        $ci->db->select('IF(route.parent_url <> \'\', concat(route.parent_url, \'/\', route.url), route.url) as full_url, content.*', FALSE);
        $ci->db->join('route', 'route.id=content.route_id');

        if ($language['identif'] == $ci->uri->segment(1)) {
            $ci->db->where('lang_alias', $pageId);
            $ci->db->where('lang', $language['id']);
        } else {
            $ci->db->where('content.id', $pageId);
        }

        $query = $ci->db->get('content');

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }

        return FALSE;
    }
}

if (! function_exists('getCategory')) {
    function getCategory($category_id = 0)
    {
        $ci = & get_instance();
        return $ci->lib_category->get_category($category_id);
    }
}

if (! function_exists('getSubCategoriesTree')) {
    function getSubCategoriesTree($category)
    {
        $ci = & get_instance();

        $cacheKey = 'getSubCategoriesTree' . $category['id'];
        if (($result = $ci->cache->fetch($cacheKey)) === false) {
            $i = 0;
            while($category['parent_id'] != 0) {
                $i++;
                $category = getCategory($category['parent_id']);
            }

            $build = $ci->lib_category->build();

            foreach ($build as $key => $item) {
                if ($category['id'] == $item['id']) {
                    $result = $item;
                }
            }

            $ci->cache->store($cacheKey, $result);
        }

        return $result;
    }
}

if (! function_exists('getAlbum')) {
    function getAlbum($id = 0, $include_images = TRUE, $limit = 0, $page = 0, $locale = null)
    {
        $ci = & get_instance();
        $ci->load->module('gallery');
        return $ci->load->model('gallery_m')->get_album($id, $include_images, $limit, $page, $locale);
    }
}

if (! function_exists('getAlbums')) {
    function getAlbums($categoryId = 0, $sortBy = 'date', $sortOrder = 'desc')
    {
        $ci = & get_instance();
        return $ci->load->module('gallery')->load->model('gallery_m')->get_albums($sortBy, $sortOrder, $categoryId);
    }
}

if (! function_exists('truncate')) {
    function truncate($inputText, $charsNum)
    {
        if (mb_strlen($inputText) <= $charsNum) {
            return $inputText;
        }
        
        $outputText = $inputText . " ";
        $outputText = substr($outputText, 0, $charsNum);
        $outputText = substr($outputText, 0, strrpos($outputText," "));
        $outputText = $outputText . "...";
        return $outputText;
    }
}

if (! function_exists('connectFields')) {
    function connectFields(&$item, $type = 'page')
    {
        $ci = & get_instance();
        $item = $ci->load->module('cfcm')->connect_fields($item, $type);
    }
}

if(! function_exists('detectUserCountry'))
{
    function detectUserCountry()
    {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"] ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];

        $SNG = array('ru', 'az', 'am', 'by', 'kz', 'kg', 'md', 'tj', 'tm', 'uz', 'ua', 'ge');
        $xml = simplexml_load_file('http://ipgeobase.ru:7020/geo?ip=' . $ip);

        if ($xml->ip->country) {
            $country = strtolower($xml->ip->country);
            $country = in_array($country, $SNG) ? 'ru' : 'en';
        } else {
            $country = MY_Controller::defaultLocale();
        }

        return $country;
    }
}

if(! function_exists('toArray')) {
    function toArray(&$item)
    {
        if (!is_array($item)){
            $item = [$item];
        }
    }
}
// Проверка на бота
if(! function_exists('detectSearchBot')) {
    function detectSearchBot()
    {
        $bot = false;

        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        if (strpos($agent,'yandex')!==false || strpos($agent,'yadirect')!==false || strpos($agent,'bond')!==false){
            $bot='yandex';
        }
        elseif (strpos($agent,'google')!==false){
            $bot='google';
        }
        elseif (strpos($agent,'stackrambler')!==false){
            $bot='rambler';
        }
        elseif (strpos($agent,'aport')!==false){
            $bot='aport';
        }
        elseif (strpos($agent,'yahoo')!==false){
            $bot='yahoo';
        }
        elseif (strpos($agent,'msnbot')!==false){
            $bot='msn';
        }
        elseif (strpos($agent,'bingbot')!==false){
            $bot='bing';
        }
        elseif(strpos($agent,'baiduspider')!==false){
            $bot='baidu';
        }
        elseif(strpos($agent,'facebot')!==false || strpos($agent,'facebook')!==false){
            $bot='facebook';
        }
        elseif(strpos($agent,'vkshare')!==false){
            $bot='vkshare';
        }
        elseif(strpos($agent,'tumblr')!==false){
            $bot='tumblr';
        }
        elseif(strpos($agent,'linkedin')!==false){
            $bot='linkedin';
        }
        elseif(strpos($agent,'twitter')!==false){
            $bot='twitter';
        }

        return $bot;
    }
}

if (!function_exists('testVal')) {
    function testVal() {
        $value = func_get_args();
        foreach ($value as $key => $v) {
            if (is_array($v) || is_object($v)){
                echo "<pre>";
                print_r($v);
                echo "</pre>";
            }elseif (is_bool($v)){
                echo (int)$v . "<br>";
            }else{
                echo $v . "<br>";
            }
        }
        exit();
    }
}

if (! function_exists('criticalError')) {
    function criticalError($message)
    {
        $to      = 'lesnik.ne@mail.ru';
        $theme   = 'Ошибка на сайте ' . $_SERVER['HTTP_HOST'];
        $header  = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset: utf8\r\n";
        mail($to, $theme, htmlspecialchars_decode($message), $header);
    }
}

if (! function_exists('isStorePage')) {
    function isStorePage()
    {
        $ci = & get_instance();

        if ($ci->core->core_data['data_type'] !== 'module') {
            return false;
        }

        if (! in_array('store', $ci->uri->segments)){
            return false;
        }

        return true;
    }
}

if (! function_exists('isMobile')) {
    function isMobile()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        $blackList = ['ipad'];

        foreach ($blackList as $item) {
            if (strpos($agent, $item) !== false) return false;
        }

        $mobileList = ['midp','samsung','nokia','j2me',
            'docomo','novarra','palmos','palmsource','opwv','chtml',
            'pda','mmp','blackberry','mib','symbian','wireless','nokia',
            'hand','mobi','phone','cdm','upb','audio','SIE','SEC','samsung',
            'HTC','mot-','mitsu','sagem','sony','alcatel','lg','eric','vx',
            'NEC','philips','mmm','xx','panasonic','sharp','wap','sch',
            'rover','pocket','benq','java','pt','pg','vox','amoi','bird',
            'compal','kg','voda','sany','kdd','dbt','sendo','sgh','gradi',
            'jb','dddi','moto','iphone','android'];

        foreach ($mobileList as $item) {
            if (strpos($agent, $item) !== false) return true;
        }

        return false;
    }
}

if (! function_exists('HTMLToString')) {
    function HTMLToString($str)
    {
        $str = strip_tags($str);
        $str = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $str);
        $str = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $str);
        return $str;
    }
}

if (! function_exists('isMainPage')) {
    function isMainPage()
    {
        $ci = & get_instance();
        return ($ci->core->core_data['data_type'] === 'main' ? true : false);
    }
}

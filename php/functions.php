<?php

/*

Jappix - An open social platform
These are the PHP functions for Jappix

~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~

License: AGPL
Authors: Valérian Saliou, Emmanuel Gil Peyrot, Mathieui, Olivier Migeot
Last revision: 30/01/11

*/

// The function to check if Jappix is already installed
function isInstalled() {
	if(!file_exists(JAPPIX_BASE.'/store/conf/installed.xml'))
		return false;
	
	return true;
}

// The function to check if this is a static server
function isStatic() {
	if(parse_url(HOST_STATIC, PHP_URL_HOST) == $_SERVER['HTTP_HOST'])
		return true;
	
	return false;
}

// The function to get the users.xml file hashed name
function usersConfName() {
	$conf_dir = JAPPIX_BASE.'/store/conf';
	
	// No conf folder?
	if(!is_dir($conf_dir))
		return '';
	
	// Read the conf folder
	$conf_scan = scandir($conf_dir.'/');
	$conf_name = '';
	
	// Loop the XML files
	foreach($conf_scan as $current) {
		if(preg_match('/(.+)(\.users\.xml)($)/', $current)) {
			$conf_name = $current;
			
			break;
		}
   	}
   	
   	// Return the users file name
   	return $conf_name;
}

// The function to write a XML file
function writeXML($type, $xmlns, $xml) {
	// Generate the file path
	$conf_path = JAPPIX_BASE.'/store/'.$type.'/';
	$conf_name = $xmlns.'.xml';
	
	// Secured stored file?
	if(($type == 'conf') && ($xmlns == 'users')) {
		// Get the secured file name
		$conf_secured = usersConfName();
		
		// Does this file exist?
		if($conf_secured)
			$conf_name = $conf_secured;
		else
			$conf_name = hash('sha256', rand(1, 99999999).time()).'.users.xml';
	}
	
	// Generate the file complete path
	$conf_file = $conf_path.$conf_name;
	
	// Write the installed marker
	$gen_xml = '<?xml version="1.0" encoding="utf-8" ?>
<jappix xmlns="jappix:'.$type.':'.$xmlns.'">
	'.trim($xml).'
</jappix>';
	
	file_put_contents($conf_file, $gen_xml);
	
	return true;
}

// The function to read a XML file
function readXML($type, $xmlns) {
	// Generate the file path
	$conf_path = JAPPIX_BASE.'/store/'.$type.'/';
	$conf_name = $xmlns.'.xml';
	
	// Secured stored file?
	if(($type == 'conf') && ($xmlns == 'users')) {
		// Get the secured file name
		$conf_secured = usersConfName();
		
		// Does this file exist?
		if($conf_secured)
			$conf_name = $conf_secured;
	}
	
	// Generate the file complete path
	$conf_file = $conf_path.$conf_name;
	
	if(file_exists($conf_file))
		return file_get_contents($conf_file);
	
	return false;
}

// The function to get the Jappix app. current version
function getVersion() {
	$file = file_get_contents(JAPPIX_BASE.'/VERSION');
	$version = trim($file);
	
	return $version;
}

// The function to detect the user's language
function checkLanguage() {
	// If the user defined a language
	if(isset($_GET['l']) && !empty($_GET['l'])) {
		// We define some stuffs
		$defined_lang = strtolower($_GET['l']);
		$lang_file = JAPPIX_BASE.'/lang/'.$defined_lang.'/LC_MESSAGES/main.mo';
		
		if($defined_lang == 'en')
			$lang_found = true;
		else
			$lang_found = file_exists($lang_file);
		
		// We check if the asked translation exists
		if($lang_found) {
			$lang = $defined_lang;
			
			// Write a cookie
			setcookie('jappix_locale', $lang, (time() + 31536000));
			
			return $lang;
		}
	}
	
	// No language has been defined, but a cookie is stored
	if(isset($_COOKIE['jappix_locale'])) {
		$check_cookie = $_COOKIE['jappix_locale'];
		
		// The cookie has a value, check this value
		if($check_cookie && (file_exists(JAPPIX_BASE.'/lang/'.$check_cookie.'/LC_MESSAGES/main.mo') || ($check_cookie == 'en')))
			return $check_cookie;
	}
	
	// No cookie defined (or an unsupported value), naturally, we check the browser language
	if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		return 'en';
	
	// We get the language of the browser
	$nav_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$check_en = strtolower($nav_langs[0]);
	
	// We check if this is not english
	if($check_en == 'en')
		return 'en';
	
	$order = array();
	
	foreach($nav_langs as $entry) {
		$indice = explode('=', $entry);
		$lang = strtolower(substr(trim($indice[0]), 0, 2));
		
		if(!isset($indice[1]) || !$indice[1])
			$indice = 1;
		else
			$indice = $indice[1];
		
		$order[$lang] = $indice;
	}
	
	arsort($order);
	
	foreach($order as $nav_lang => $val) {
		$lang_found = file_exists(JAPPIX_BASE.'/lang/'.$nav_lang.'/LC_MESSAGES/main.mo');
		
		if($lang_found)
			return $nav_lang;
	}
	
	// If Jappix doen't know that language, we include the english translation
	return 'en';
}

// The function to convert a ISO language code to its full name
function getLanguageName($code) {
	$known = array(
		'aa' => 'Afaraf',
		'ab' => 'Аҧсуа',
		'ae' => 'Avesta',
		'af' => 'Afrikaans',
		'ak' => 'Akan',
		'am' => 'አማርኛ',
		'an' => 'Aragonés',
		'ar' => 'العربية',
		'as' => 'অসমীয়া',
		'av' => 'авар мацӀ',
		'ay' => 'Aymar aru',
		'az' => 'Azərbaycan dili',
		'ba' => 'башҡорт теле',
		'be' => 'Беларуская',
		'bg' => 'български език',
		'bh' => 'भोजपुरी',
		'bi' => 'Bislama',
		'bm' => 'Bamanankan',
		'bn' => 'বাংলা',
		'bo' => 'བོད་ཡིག',
		'br' => 'Brezhoneg',
		'bs' => 'Bosanski jezik',
		'ca' => 'Català',
		'ce' => 'нохчийн мотт',
		'ch' => 'Chamoru',
		'co' => 'Corsu',
		'cr' => 'ᓀᐦᐃᔭᐍᐏᐣ',
		'cs' => 'Česky',
		'cu' => 'Словѣньскъ',
		'cv' => 'чӑваш чӗлхи',
		'cy' => 'Cymraeg',
		'da' => 'Dansk',
		'de' => 'Deutsch',
		'dv' => 'ދިވެހި',
		'dz' => 'རྫོང་ཁ',
		'ee' => 'Ɛʋɛgbɛ',
		'el' => 'Ελληνικά',
		'en' => 'English',
		'eo' => 'Esperanto',
		'es' => 'Español',
		'et' => 'Eesti keel',
		'eu' => 'Euskara',
		'fa' => 'فارسی',
		'ff' => 'Fulfulde',
		'fi' => 'Suomen kieli',
		'fj' => 'Vosa Vakaviti',
		'fo' => 'Føroyskt',
		'fr' => 'Français',
		'fy' => 'Frysk',
		'ga' => 'Gaeilge',
		'gd' => 'Gàidhlig',
		'gl' => 'Galego',
		'gn' => 'Avañe\'ẽ',
		'gu' => 'ગુજરાતી',
		'gv' => 'Ghaelg',
		'ha' => 'هَوُسَ',
		'he' => 'עברית',
		'hi' => 'हिन्दी',
		'ho' => 'Hiri Motu',
		'hr' => 'Hrvatski',
		'ht' => 'Kreyòl ayisyen',
		'hu' => 'Magyar',
		'hy' => 'Հայերեն',
		'hz' => 'Otjiherero',
		'ia' => 'Interlingua',
		'id' => 'Bahasa',
		'ie' => 'Interlingue',
		'ig' => 'Igbo',
		'ii' => 'ꆇꉙ',
		'ik' => 'Iñupiaq',
		'io' => 'Ido',
		'is' => 'Íslenska',
		'it' => 'Italiano',
		'iu' => 'ᐃᓄᒃᑎᑐᑦ',
		'ja' => '日本語',
		'jv' => 'Basa Jawa',
		'ka' => 'ქართული',
		'kg' => 'KiKongo',
		'ki' => 'Gĩkũyũ',
		'kj' => 'Kuanyama',
		'kk' => 'Қазақ тілі',
		'kl' => 'Kalaallisut',
		'km' => 'ភាសាខ្មែរ',
		'kn' => 'ಕನ್ನಡ',
		'ko' => '한 국어',
		'kr' => 'Kanuri',
		'ks' => 'कश्मीरी',
		'ku' => 'Kurdî',
		'kv' => 'коми кыв',
		'kw' => 'Kernewek',
		'ky' => 'кыргыз тили',
		'la' => 'Latine',
		'lb' => 'Lëtzebuergesch',
		'lg' => 'Luganda',
		'li' => 'Limburgs',
		'ln' => 'Lingála',
		'lo' => 'ພາສາລາວ',
		'lt' => 'Lietuvių kalba',
		'lu' => 'cilubà',
		'lv' => 'Latviešu valoda',
		'mg' => 'Fiteny malagasy',
		'mh' => 'Kajin M̧ajeļ',
		'mi' => 'Te reo Māori',
		'mk' => 'македонски јазик',
		'ml' => 'മലയാളം',
		'mn' => 'Монгол',
		'mo' => 'лимба молдовеняскэ',
		'mr' => 'मराठी',
		'ms' => 'Bahasa Melayu',
		'mt' => 'Malti',
		'my' => 'ဗမာစာ',
		'na' => 'Ekakairũ Naoero',
		'nb' => 'Norsk bokmål',
		'nd' => 'isiNdebele',
		'ne' => 'नेपाली',
		'ng' => 'Owambo',
		'nl' => 'Nederlands',
		'nn' => 'Norsk nynorsk',
		'no' => 'Norsk',
		'nr' => 'Ndébélé',
		'nv' => 'Diné bizaad',
		'ny' => 'ChiCheŵa',
		'oc' => 'Occitan',
		'oj' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ',
		'om' => 'Afaan Oromoo',
		'or' => 'ଓଡ଼ିଆ',
		'os' => 'Ирон æвзаг',
		'pa' => 'ਪੰਜਾਬੀ',
		'pi' => 'पािऴ',
		'pl' => 'Polski',
		'ps' => 'پښتو',
		'pt' => 'Português',
		'qu' => 'Runa Simi',
		'rm' => 'Rumantsch grischun',
		'rn' => 'kiRundi',
		'ro' => 'Română',
		'ru' => 'Русский',
		'rw' => 'Kinyarwanda',
		'sa' => 'संस्कृतम्',
		'sc' => 'sardu',
		'sd' => 'सिन्धी',
		'se' => 'Davvisámegiella',
		'sg' => 'Yângâ tî sängö',
		'sh' => 'Српскохрватски',
		'si' => 'සිංහල',
		'sk' => 'Slovenčina',
		'sl' => 'Slovenščina',
		'sm' => 'Gagana fa\'a Samoa',
		'sn' => 'chiShona',
		'so' => 'Soomaaliga',
		'sq' => 'Shqip',
		'sr' => 'српски језик',
		'ss' => 'SiSwati',
		'st' => 'seSotho',
		'su' => 'Basa Sunda',
		'sv' => 'Svenska',
		'sw' => 'Kiswahili',
		'ta' => 'தமிழ்',
		'te' => 'తెలుగు',
		'tg' => 'тоҷикӣ',
		'th' => 'ไทย',
		'ti' => 'ትግርኛ',
		'tk' => 'Türkmen',
		'tl' => 'Tagalog',
		'tn' => 'seTswana',
		'to' => 'faka Tonga',
		'tr' => 'Türkçe',
		'ts' => 'xiTsonga',
		'tt' => 'татарча',
		'tw' => 'Twi',
		'ty' => 'Reo Mā`ohi',
		'ug' => 'Uyƣurqə',
		'uk' => 'українська',
		'ur' => 'اردو',
		'uz' => 'O\'zbek',
		've' => 'tshiVenḓa',
		'vi' => 'Tiếng Việt',
		'vo' => 'Volapük',
		'wa' => 'Walon',
		'wo' => 'Wollof',
		'xh' => 'isiXhosa',
		'yi' => 'ייִדיש',
		'yo' => 'Yorùbá',
		'za' => 'Saɯ cueŋƅ',
		'zh' => '中文',
		'zu' => 'isiZulu'
	);
	
	if(isset($known[$code]))
		return $known[$code];
	
	return null;
}

// The function to know if a language is right-to-left
function isRTL($code) {
	switch($code) {
		// RTL language
		case 'ar':
		case 'he':
		case 'dv':
		case 'ur':
			$is_rtl = true;
			
			break;
		
		// LTR language
		default:
			$is_rtl = false;
			
			break;
	}
	
	return $is_rtl;
}

// The function to set the good localized <html /> tag
function htmlTag($locale) {
	// Initialize the tag
	$html = '<html xml:lang="'.$locale.'" lang="'.$locale.'" dir="';
	
	// Set the good text direction (TODO)
	/* if(isRTL($locale))
		$html .= 'rtl';
	else
		$html .= 'ltr'; */
	
	$html .= 'ltr';
	
	// Close the tag
	$html .= '">';
	
	echo($html);
}

// The function which generates the available locales list
function availableLocales($active_locale) {
	// Initialize
	$scan = scandir(JAPPIX_BASE.'/lang/');
	$list = array();
	
	// Loop the available languages
	foreach($scan as $current_id) {
		// Get the current language name
		$current_name = getLanguageName($current_id);
		
		// Not valid?
		if(($current_id == $active_locale) || ($current_name == null))
			continue;
		
		// Add this to the list
		$list[$current_id] = $current_name;
   	}
   	
   	return $list;
}

// The function which generates the language switcher hidden part
function languageSwitcher($active_locale) {
	// Initialize
	$keep_get = keepGet('l', false);
	$list = availableLocales($active_locale);
	$html = '';
	
	// Generate the HTML code
	foreach($list as $current_id => $current_name)
		$html .= '<a href="./?l='.$current_id.$keep_get.'">'.htmlspecialchars($current_name).'</a>, ';
   	
   	// Output the HTML code
   	return $html;
}

// The function to generate a strong hash
function genStrongHash($string) {
	// Initialize
	$i = 0;
	
	// Loop to generate a incredibly strong hash (can be a bit slow)
	while($i < 10) {
		$string = hash('sha256', $string);
		
		$i++;
	}
	
	return $string;
}

// The function to generate the version hash
function genHash($version) {
	// Get the configuration files path
	$conf_path = JAPPIX_BASE.'/store/conf/';
	$conf_main = $conf_path.'main.xml';
	$conf_hosts = $conf_path.'hosts.xml';
	$conf_background = $conf_path.'background.xml';
	
	// Get the hash of the main configuration file
	if(file_exists($conf_main))
		$hash_main = md5_file($conf_main);
	else
		$hash_main = '0';
	
	// Get the hash of the main configuration file
	if(file_exists($conf_hosts))
		$hash_hosts = md5_file($conf_hosts);
	else
		$hash_hosts = '0';
	
	// Get the hash of the background configuration file
	if(file_exists($conf_background))
		$hash_background = md5_file($conf_background);
	else
		$hash_background = '0';
	
	return md5($version.$hash_main.$hash_hosts.$hash_background);
}

// The function to hide the error messages
function hideErrors() {
	if(!isDeveloper())
		ini_set('display_errors','off');
}

// The function to check BOSH proxy is enabled
function BOSHProxy() {
	if(BOSH_PROXY == 'on')
		return true;
	
	return false;
}

// The function to check compression is enabled
function hasCompression() {
	if(COMPRESSION == 'on')
		return true;
	
	return false;
}

// The function to check compression is available with the current client
function canCompress() {
	// Compression allowed by admin & browser?
	if(hasCompression() && (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')))
		return true;
	
	return false;
}

// The function to check HTTPS storage is allowed
function httpsStorage() {
	if(HTTPS_STORAGE == 'on')
		return true;
	
	return false;
}

// The function to check HTTPS storage must be forced
function httpsForce() {
	if((HTTPS_FORCE == 'on') && sslCheck())
		return true;
	
	return false;
}

// The function to check we use HTTPS
function useHttps() {
	if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))
		return true;
	
	return false;
}

// The function to compress the output pages
function compressThis() {
	if(canCompress() && !isDeveloper())
		ob_start('ob_gzhandler');
}

// The function to choose one file get with get.php or a liste of resources
function multiFiles() {
	if(MULTI_FILES == 'on')
		return true;
	
	return false;
}

function getFiles($h, $l, $t, $g, $f) {
	// Define the good path to the Get API
	if(HOST_STATIC != '.')
		$path_to = HOST_STATIC.'/';
	else
		$path_to = JAPPIX_BASE.'/';
		
	if(!multiFiles()) {
		$values = array();
		if ($h)
			$values[] = 'h='.$h;
		if ($l)
			$values[] = 'l='.$l;
		if ($t)
			$values[] = 't='.$t;
		if ($g)
			$values[] = 'g='.$g;
		if ($f)
			$values[] = 'f='.$f;
		
		return $path_to.'php/get.php?'.implode('&amp;', $values);
	}
	
	if($g && !empty($g) && preg_match('/^(\S+)\.xml$/', $g) && preg_match('/^(css|js)$/', $t) && isSafe($g) && file_exists('xml/'.$g)) {
		$xml_data = file_get_contents('xml/'.$g);
		
		// Any data?
		if($xml_data) {
			$xml_read = new SimpleXMLElement($xml_data);
			$xml_parse = $xml_read->$t;
			
			// Files were added to the list before (with file var)?
			if($f)
				$f .= '~'.$xml_parse;
			else
				$f = $xml_parse;
		}
	}
	
	// Explode the f string
	if(strpos($f, '~') != false)
		$array = explode('~', $f);
	else
		$array = array($f);
	
	$a = array();
	foreach($array as $file)
		$a[] = $path_to.$t.'/'.$file;

	if (count($a) == 1)
		return $a[0];

	return $a;
}

function echoGetFiles($h, $l, $t, $g, $f) {
	if ($t == 'css')
		$pattern = '<link rel="stylesheet" href="%s" type="text/css" media="all" />';
	else if ($t == 'js')
		$pattern = '<script type="text/javascript" src="%s"></script>';
	
	$files = getFiles($h, $l, $t, $g, $f);

	if (is_string($files))
		printf($pattern, $files);
	else {
		$c = count($files)-1;
		for($i=0; $i<=$c; $i++) {
			if ($i)
				echo '	';
			printf($pattern, $files[$i]);
			if ($i != $c)
				echo "\n";
		}
	}
}

// The function to check if anonymous mode is authorized
function anonymousMode() {
	if(isset($_GET['r']) && !empty($_GET['r']) && HOST_ANONYMOUS && (ANONYMOUS == 'on'))
		return true;
	else
		return false;
}

// The function to quickly translate a string
function _e($string) {
	echo T_gettext($string);
}

// The function to check the encrypted mode
function sslCheck() {
	if(ENCRYPTION == 'on')
		return true;
	else
		return false;
}

// The function to return the encrypted link
function sslLink() {
	// Using HTTPS?
	if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))
		$link = '<a class="home-images uncrypted" href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">'.T_('Uncrypted').'</a>';
	
	// Using HTTP?
	else
		$link = '<a class="home-images crypted" href="https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">'.T_('Encrypted').'</a>';
	
	return $link;
}

// The function to get the Jappix static URL
function staticURL() {
	// Check for HTTPS
	$protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
	
	// Full URL
	$url = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	return $url;
}

// The function to get the Jappix location (only from Get API!)
function staticLocation() {
	// Filter the URL
	return preg_replace('/((.+)\/)php\/get\.php(\S)+$/', '$1', staticURL());
}

// The function to include a translation file
function includeTranslation($locale, $domain) {
	T_setlocale(LC_MESSAGES, $locale);
	T_bindtextdomain($domain, JAPPIX_BASE.'/lang');
	T_bind_textdomain_codeset($domain, 'UTF-8');
	T_textdomain($domain);
}

// The function to check the cache presence
function hasCache($hash) {
	if(file_exists(JAPPIX_BASE.'/store/cache/'.$hash.'.cache'))
		return true;
	else
		return false;
}

// The function to check if developer mode is enabled
function isDeveloper() {
	if(DEVELOPER == 'on')
		return true;
	else
		return false;
}

// The function to get a file extension
function getFileExt($name) {
	return strtolower(preg_replace('/^(.+)(\.)(.+)$/i', '$3', $name));
}

// The function to get a file type
function getFileType($ext) {
	switch($ext) {
		// Images
		case 'jpg':
		case 'jpeg':
		case 'png':
		case 'bmp':
		case 'gif':
		case 'tif':
		case 'svg':
		case 'psp':
		case 'xcf':
			$file_type = 'image';
			
			break;
		
		// Videos
		case 'ogv':
		case 'mkv':
		case 'avi':
		case 'mov':
		case 'mp4':
		case 'm4v':
		case 'wmv':
		case 'asf':
		case 'mpg':
		case 'mpeg':
		case 'ogm':
		case 'rmvb':
		case 'rmv':
		case 'qt':
		case 'flv':
		case 'ram':
		case '3gp':
		case 'avc':
			$file_type = 'video';
			
			break;
		
		// Sounds
		case 'oga':
		case 'ogg':
		case 'mka':
		case 'flac':
		case 'mp3':
		case 'wav':
		case 'm4a':
		case 'wma':
		case 'rmab':
		case 'rma':
		case 'bwf':
		case 'aiff':
		case 'caf':
		case 'cda':
		case 'atrac':
		case 'vqf':
		case 'au':
		case 'aac':
		case 'm3u':
		case 'mid':
		case 'mp2':
		case 'snd':
		case 'voc':
			$file_type = 'audio';
			
			break;
		
		// Documents
		case 'pdf':
		case 'odt':
		case 'ott':
		case 'sxw':
		case 'stw':
		case 'ots':
		case 'sxc':
		case 'stc':
		case 'sxi':
		case 'sti':
		case 'pot':
		case 'odp':
		case 'ods':
		case 'doc':
		case 'docx':
		case 'docm':
		case 'xls':
		case 'xlsx':
		case 'xlsm':
		case 'xlt':
		case 'ppt':
		case 'pptx':
		case 'pptm':
		case 'pps':
		case 'odg':
		case 'otp':
		case 'sxd':
		case 'std':
		case 'std':
		case 'rtf':
		case 'txt':
		case 'htm':
		case 'html':
		case 'shtml':
		case 'dhtml':
		case 'mshtml':
			$file_type = 'document';
			
			break;
		
		// Packages
		case 'tgz':
		case 'gz':
		case 'tar':
		case 'ar':
		case 'cbz':
		case 'jar':
		case 'tar.7z':
		case 'tar.bz2':
		case 'tar.gz':
		case 'tar.lzma':
		case 'tar.xz':
		case 'zip':
		case 'xz':
		case 'rar':
		case 'bz':
		case 'deb':
		case 'rpm':
		case '7z':
		case 'ace':
		case 'cab':
		case 'arj':
		case 'msi':
			$file_type = 'package';
			
			break;
		
		// Others
		default:
			$file_type = 'other';
			
			break;
	}
	
	return $file_type;
}

// The function to keep the current GET vars
function keepGet($current, $no_get) {
	// Get the HTTP GET vars
	$request = $_SERVER['REQUEST_URI'];
	
	if(strrpos($request, '?') === false)
		$get = '';
	
	else {
		$uri = explode('?', $request);
		$get = $uri[1];
	}
	
	// Remove the items we don't want here
	$proper = str_replace('&', '&amp;', $get);
	$proper = preg_replace('/((^)|(&amp;))(('.$current.'=)([^&]+))/i', '', $proper);
	
	// Nothing at the end?
	if(!$proper)
		return '';
	
	// We have no defined GET var
	if($no_get) {
		// Remove the first "&" if it appears
		if(preg_match('/^(&(amp;)?)/i', $proper))
			$proper = preg_replace('/^(&(amp;)?)/i', '', $proper);
		
		// Add the first "?"
		$proper = '?'.$proper;
	}
	
	// Add a first "&" if there is no one and no defined GET var
	else if(!$no_get && (substr($proper, 0, 1) != '&') && (substr($proper, 0, 5) != '&amp;'))
		$proper = '&amp;'.$proper;
	
	return $proper;
}

// Escapes regex special characters for in-regex usage
function escapeRegex($string) {
	return preg_replace('/[-[\]{}()*+?.,\\^$|#]/', '\\$&', $string);
}

// Generates the security HTML code
function securityHTML() {
	return '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Jappix - Forbidden</title>
</head>

<body>
	<h1>Forbidden</h1>
	<h4>This is a private folder</h4>
</body>

</html>';
}

// Checks if a relative server path is safe
function isSafe($path) {
	// Mhh, someone is about to nasty stuffs (previous folder, or executable scripts)
	if(preg_match('/\.\.\//', $path) || preg_match('/index\.html?$/', $path) || preg_match('/(\.)((php([0-9]+)?)|(aspx?)|(cgi)|(rb)|(py)|(pl)|(jsp)|(ssjs)|(lasso)|(dna)|(tpl)|(smx)|(cfm))$/i', $path))
		return false;
	
	return true;
}

// Set the good unity for a size in bytes
function formatBytes($bytes, $precision = 2) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	
	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);
	
	$bytes /= pow(1024, $pow);
	
	return round($bytes, $precision) . ' ' . $units[$pow];
}

// Converts a human-readable bytes value to a computer one
function humanToBytes($string) {
	// Values array
	$values = array(
		       	'K' => '000',
		       	'M' => '000000',
		       	'G' => '000000000',
		       	'T' => '000000000000',
		       	'P' => '000000000000000',
		       	'E' => '000000000000000000',
		       	'Z' => '000000000000000000000',
		       	'Y' => '000000000000000000000000'
		       );
	
	// Filter the string
	foreach($values as $key => $zero)
		$string = str_replace($key, $zero, $string);
	
	// Converts the string into an integer
	$string = intval($string);
	
	return $string;
}

// Get the maximum file upload size
function uploadMaxSize() {
	// Not allowed to upload files?
	if(ini_get('file_uploads') != 1)
		return 0;
	
	// Upload maximum file size
	$upload = humanToBytes(ini_get('upload_max_filesize'));
	
	// POST maximum size
	$post = humanToBytes(ini_get('post_max_size'));
	
	// Return the lowest value
	if($upload <= $post)
		return $upload;
	
	return $post;
}

// Normalizes special chars
function normalizeChars($string) {
	$table = array(
		'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
		'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
		'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
		'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
		'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
		'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
		'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
		'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r'
	);
	
	return strtr($string, $table);
}

// Filters the XML special chars for the SVG drawer
function filterSpecialXML($string) {
	// Strange thing: when $string = 'Mises à jour' -> bug! but 'Mise à jour' -> ok!
	$string = normalizeChars($string);
	
	// Encodes with HTML special chars
	$string = htmlspecialchars($string);
	
	return $string;
}

// Writes the current visit in the total file
function writeTotalVisit() {
	// Get the current time stamp
	$stamp = time();
	
	// Initialize the defaults
	$array = array(
		      	'total' => 0,
		      	'stamp' => $stamp
		      );
	
	// Try to read the saved data
	$total_data = readXML('access', 'total');
	
	// Get the XML file values
	if($total_data) {
		// Initialize the visits reading
		$read_xml = new SimpleXMLElement($total_data);
		
		// Loop the visit elements
		foreach($read_xml->children() as $current_child)
			$array[$current_child->getName()] = intval($current_child);
	}
	
	// Increment the total number of visits
	$array['total']++;
	
	// Generate the new XML data
	$total_xml = 
	'<total>'.$array['total'].'</total>
	<stamp>'.$array['stamp'].'</stamp>'
	;
	
	// Re-write the new values
	writeXML('access', 'total', $total_xml);
}

// Writes the current visit in the months file
function writeMonthsVisit() {
	// Get the current month
	$month = intval(date('m'));
	
	// Define the stats array
	$array = array();
	
	// January to August period
	if($month <= 8) {
		for($i = 1; $i <= 8; $i++)
			$array['month_'.$i] = 0;
	}
	
	// August to September period
	else {
		$i = 8;
		$j = 1;
		
		while($j <= 3) {
			// Last year months
			if(($i >= 8) && ($i <= 12))
				$array['month_'.$i++] = 0;
			
			// First year months
			else
				$array['month_'.$j++] = 0;
		}
	}
	
	// Try to read the saved data
	$months_data = readXML('access', 'months');
	
	// Get the XML file values
	if($months_data) {
		// Initialize the visits reading
		$read_xml = new SimpleXMLElement($months_data);
		
		// Loop the visit elements
		foreach($read_xml->children() as $current_child) {
			$current_month = $current_child->getName();
			
			// Parse the current month id
			$current_id = intval(preg_replace('/month_([0-9]+)/i', '$1', $current_month));
			
			// Is this month still valid?
			if((($month <= 8) && ($current_id <= $month)) || (($month >= 8) && ($current_id >= 8) && ($current_id <= $month)))
				$array[$current_month] = intval($current_child);
		}
	}
	
	// Increment the current month value
	$array['month_'.$month]++;
	
	// Generate the new XML data
	$months_xml = '';
	
	foreach($array as $array_key => $array_value)
		$months_xml .= "\n".'	<'.$array_key.'>'.$array_value.'</'.$array_key.'>';
	
	// Re-write the new values
	writeXML('access', 'months', $months_xml);
}

// Writes the current visit to the storage file
function writeVisit() {
	// Write total visits
	writeTotalVisit();
	
	// Write months visits
	writeMonthsVisit();
}

// Returns the default background array
function defaultBackground() {
	// Define the default values
	$background_default = array(
			      	'type' => 'default',
			      	'image_file' => '',
			      	'image_repeat' => 'repeat-x',
			      	'image_horizontal' => 'center',
			      	'image_vertical' => 'top',
			      	'image_adapt' => 'off',
			      	'image_color' => '#cae1e9',
			      	'color_color' => '#cae1e9'
			      );
	
	return $background_default;
}

// Reads the notice configuration
function readNotice() {
	// Read the notice configuration XML
	$notice_data = readXML('conf', 'notice');
	
	// Define the default values
	$notice_default = array(
			  	'type' => 'none',
			  	'notice' => ''
			  );
	
	// Stored data array
	$notice_conf = array();
	
	// Read the stored values
	if($notice_data) {
		// Initialize the notice configuration XML data
		$notice_xml = new SimpleXMLElement($notice_data);
		
		// Loop the notice configuration elements
		foreach($notice_xml->children() as $notice_child)
			$notice_conf[$notice_child->getName()] = utf8_decode($notice_child);
	}
	
	// Checks no value is missing in the stored configuration
	foreach($notice_default as $notice_name => $notice_value) {
		if(!isset($notice_conf[$notice_name]) || empty($notice_conf[$notice_name]))
			$notice_conf[$notice_name] = $notice_default[$notice_name];
	}
	
	return $notice_conf;
}

// The function to get the admin users
function getUsers() {
	// Try to read the XML file
	$data = readXML('conf', 'users');
	$array = array();
	
	// Any data?
	if($data) {
		$read = new SimpleXMLElement($data);
		
		// Check the submitted user exists
		foreach($read->children() as $child) {
			// Get the node attributes
			$attributes = $child->attributes();
			
			// Push the attributes to the global array (converted into strings)
			$array[$attributes['name'].''] = $attributes['password'].'';
		}
	}
	
	return $array;
}

// Manages users
function manageUsers($action, $array) {
	// Try to read the old XML file
	$users_array = getUsers();
	
	// What must we do?
	switch($action) {
		// Add some users
		case 'add':
			foreach($array as $array_user => $array_password)
				$users_array[$array_user] = genStrongHash($array_password);
			
			break;
		
		// Remove some users
		case 'remove':
			foreach($array as $array_user) {
				// Not the last user?
				if(count($users_array) > 1)
					unset($users_array[$array_user]);
			}
			
			break;
	}
	
	// Regenerate the XML
	$users_xml = '';
	
	foreach($users_array as $users_name => $users_password)
		$users_xml .= "\n".'	<user name="'.htmlspecialchars($users_name).'" password="'.$users_password.'" />';
	
	// Write the main configuration
	writeXML('conf', 'users', $users_xml);
}

?>

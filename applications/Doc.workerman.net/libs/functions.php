<?php
// Enable for debugging
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

/*
* WARNING: DO NOT EDIT THIS FILE.
* If you edit this file, you will not be able to update to new versions of
* Daux.io without overwritting your changes. Instead use the config.json file
* in the /docs folder to customize your documentation.
*
* Want to add a new feature? Send me a pull request or open an issue - https://github.com/justinwalsh/daux.io
*
* Learn more about what you can customize here: http://daux.io
*/

require_once DOC_ROOT . '/libs/markdown_extended.php';

// Daux.io Functions
function get_options() {
	$options = array(
		'title' => "Documentation",
		'tagline' => false,
		'image' => false,
		'theme' => 'blue',
		'date_modified' => true,
		'float' => true,
		'repo' => false,
		'twitter' => array(),
		'links' => array(),
		'colors' => false,
		'clean_urls' => true,
		'google_analytics' => false,
		'piwik_analytics' => false,
        'ignore' => array(),
        'languages' => array()
	);

	// Load User Config
	$config_file = './docs/config.json';
	if (file_exists($config_file)) {
		$config = json_decode(file_get_contents($config_file), true);
		$options = array_merge($options, $config);
	}

	if ($options['theme'] !== 'custom') {
		// Load Theme
		if (!in_array($options['theme'], array("blue","navy","green","red"))) {
			echo "<strong>Daux.io Config Error:</strong><br>The theme you set is not not a valid option. Please use one of the following options: " . join(array_keys($themes), ', ') . ' or <a href="http://daux.io">learn more</a> about how to customize the colors.';
			exit;
		}
	} else {
		if (empty($options['colors'])) {
			echo '<strong>Daux.io Config Error:</strong><br>You are trying to use a custom theme, but did not setup your color options in the config. <a href="http://daux.io">Learn more</a> about how to customize the colors.';
			exit;
		}
	}

	return $options;
}

function homepage_url($tree) {
	// Check for homepage
	if (isset($tree['index'])) {
		return '/';
	} else {
		return docs_url($tree, false);
	}
}

function docs_url($tree, $branch = false) {
	// Get next branch
	if (!$branch) {
		$branch = current($tree);
	}

	if ($branch['type'] === 'file') {
		return $branch['url'];
	} else if (!empty($branch['tree'])) {
		return docs_url($branch['tree']);
	} else {
		// Try next folder...
		$branch = next($tree);
		if ($branch) {
			return docs_url($tree, $branch);
		} else {
			echo '<strong>Daux.io Config Error:</strong><br>Unable to find the first page in the /docs folder. Double check you have at least one file in the root of of the /docs folder. Also make sure you do not have any empty folders. Visit the docs to <a href="http://daux.io">learn more</a> about how the default routing works.';
			exit;
		}
	}
}

function load_page($tree, $url_params) {
    if (count($url_params) > 0) {
        $branch = find_branch($tree, $url_params);
    } else {
        $branch = current($tree);
    }

	$page = array();

	if (isset($branch['type']) && $branch['type'] == 'file') {
		$html = '';
		if ($branch['name'] !== 'index') {

			$page['title'] = $branch['title'];
			$page['modified'] = filemtime($branch['path']);

		}
		$html .= MarkdownExtended(file_get_contents($branch['path']));

		$page['html'] = $html;

	} else {

		$page['title'] = "Oh no";
		$page['html'] = "<h3>Oh No. That page doesn't exist</h3>";

	}
	

	return $page;
}


function find_branch($tree, $url_params) {
	foreach($url_params as $peice) {

		// Support for cyrillic URLs
		$peice = urldecode($peice);

		// Check for homepage
		if (empty($peice)) {
			$peice = 'index';
		}

		if (isset($tree[$peice])) {
			if ($tree[$peice]['type'] == 'folder') {
				$tree = $tree[$peice]['tree'];
			} else {
				$tree = $tree[$peice];
			}
		} else {
			return false;
		}
	}

	return $tree;
}

function url_path() {
	$url = parse_url($_SERVER['REQUEST_URI']);
	$url = $url['path'];
	return $url;
}

function url_params() {
	$url = rawurldecode(get_uri());
	$params = explode('/', trim($url, '/'));
	return $params;
}

function clean_sort($text) {
	// Remove .md file extension
	$text = str_replace('.md', '', $text);

	// Remove sort placeholder
	$parts = explode('_', $text);
	if (isset($parts[0]) && is_numeric($parts[0])) {
		unset($parts[0]);
	}
	$text = implode('_', $parts);

	return $text;
}

function clean_name($text) {
	$text = str_replace('_', ' ', $text);
	return $text;
}

function RFC3986UrlEncode($string) {
	$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
	$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
	return str_replace($entities, $replacements, urlencode($string));
}

function build_nav($tree, $url_params) {
	// Remove Index
	unset($tree['index']);

	$url_path = url_path();
	$html = '<ul class="nav nav-list">';
	foreach($tree as $key => $val) {
		// Active Tree Node
		if (isset($url_params[0]) && $url_params[0] == $val['clean']) {
			array_shift($url_params);

			// Final Node
			if ($url_path == RFC3986UrlEncode($val['url'])) {
				$html .= '<li class="active">';
			} else {
				$html .= '<li class="open">';
			}
		} else {
			$html .= '<li>';
		}

		if ($val['type'] == 'folder') {
			$html .= '<a href="#" class="aj-nav folder">'.$val['name'].'</a>';
			$html .= build_nav($val['tree'], $url_params);
		} else {
			$html .= '<a href="'.$val['url'].'">'.$val['name'].'</a>';
		}

		$html .= '</li>';
	}
	$html .= '</ul>';
	return $html;
}

function get_ignored() {
	// TODO: Add support for wildcards
	// TODO: Add support for specific paths, i.e. /Publish/Somefile.md vs. /Don't_Publish/Somefile.md
	$options = get_options();
	$default_ignored_files = array('config.json', 'cgi-bin', 'Thumbs.db');
	$default_ignored_folders = array(); // To allow for easy addition of default folders if found necessary in the future
	$user_ignored_files = array();
	$user_ignored_folders = array();
	// Check if ignore settings exist
	if(array_key_exists('ignore', $options)) {
		if(array_key_exists('files', $options['ignore'])) {
			$user_ignored_files = $options['ignore']['files'];
		}
		if(array_key_exists('folders', $options['ignore'])) {
			$user_ignored_folders = $options['ignore']['folders'];
		}
	}

	// Merge all ignore arrays together
	$all_ignored = array_merge($default_ignored_files, $default_ignored_folders, $user_ignored_files, $user_ignored_folders);

	// Return array of all ignored files and folders
	return $all_ignored;
}

function get_tree($path = '.', $clean_path = '', $title = '', $first = true, $language = null){
    $options = get_options();
    $tree = array();
    $ignore = get_ignored();
    $dh = @opendir($path);
    $index = 0;

    // Build array of paths
    $paths = array();
    while(false !== ($file = readdir($dh))){
		$paths[$file] = $file;
	}

	// Close the directory handle
	closedir($dh);

	// Sort paths
	sort($paths);
    
    if ($first && $language !== null) {
        $language_path = $language . "/";
    } else {
        $language_path = "";
    }

    // Loop through the paths
    // while(false !== ($file = readdir($dh))){
	foreach($paths as $file) {

     	// Check that this file or folder is not to be ignored
        if(!in_array($file, $ignore) && $file[0] !== '.') {
            $full_path = "$path/$file";
            $clean_sort = clean_sort($file);

            // If clean_urls is set to false and this is the first branch of the tree, append index.php to the clean_path.
            if($options['clean_urls'] == false)
            {
                if($first)
                {
                    $url = $clean_path . '/' . $language_path . 'index.php/' . $clean_sort;
                }
                else
                {
                    $url = $clean_path . '/' . $language_path . $clean_sort;
                }
            }
            else
            {
            	$url = $clean_path . '/' . $language_path . $clean_sort;
            }

        	$clean_name = clean_name($clean_sort);

        	// Title
        	if (empty($title)) {
        		$full_title = $clean_name;
        	} else {
        		$full_title = $title . ': ' . $clean_name;
        	}

            if(is_dir("$path/$file")) {
            	// Directory
            	$tree[$clean_sort] = array(
            		'type' => 'folder',
            		'name' => $clean_name,
            		'title' => $full_title,
            		'path' => $full_path,
            		'clean' => $clean_sort,
            		'url' => $url,
            		'tree'=> get_tree($full_path, $url, $full_title, false, $language)
            	);
            } else {
            	// File
            	$tree[$clean_sort] = array(
            		'type' => 'file',
            		'name' => $clean_name,
            		'title' => $full_title,
            		'path' => $full_path,
            		'clean' => $clean_sort,
            		'url' => $url,
            	);
            }
        }
     	$index++;
    }

    return $tree;
}

/**
 * Get's the current "pretty" URI from the URL.  It will also correct the QUERY_STRING server var and the $_GET array.
 * It supports all forms of mod_rewrite and the following forms of URL:
 *
 * http://example.com/index.php/foo (returns '/foo')
 * http://example.com/index.php?/foo (returns '/foo')
 * http://example.com/index.php/foo?baz=bar (returns '/foo')
 * http://example.com/index.php?/foo?baz=bar (returns '/foo')
 *
 * Similarly using mod_rewrite to remove index.php:
 * http://example.com/foo (returns '/foo')
 * http://example.com/foo?baz=bar (returns '/foo')
 *
 * @author      Dan Horrigan <http://dhorrigan.com>
 * @copyright   Dan Horrigan
 * @license     MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @param   bool    $prefix_slash   whether to return the uri with a '/' in front
 * @return  string  the uri
 */
function get_uri($prefix_slash = true)
{
    if (isset($_SERVER['PATH_INFO']))
    {
        $uri = $_SERVER['PATH_INFO'];
    }
    elseif (isset($_SERVER['REQUEST_URI']))
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
        {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        }
        elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
        {
            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        if (strncmp($uri, '?/', 2) === 0)
        {
            $uri = substr($uri, 2);
        }
        $parts = preg_split('#\?#i', $uri, 2);
        $uri = $parts[0];
        if (isset($parts[1]))
        {
            $_SERVER['QUERY_STRING'] = $parts[1];
            parse_str($_SERVER['QUERY_STRING'], $_GET);
        }
        else
        {
            $_SERVER['QUERY_STRING'] = '';
            $_GET = array();
        }
        $uri = parse_url($uri, PHP_URL_PATH);
    }
    else
    {
        // Couldn't determine the URI, so just return false
        return false;
    }

    // Do some final cleaning of the URI and return it
    return ($prefix_slash ? '/' : '').str_replace(array('//', '../'), '/', trim($uri, '/'));
}

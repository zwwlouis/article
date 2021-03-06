<?php
/**
 * Article module article route
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Zongshu Lin <zongshu@eefocus.com>
 * @since           1.0
 * @package         Module\Article
 */

namespace Module\Article\Route;

use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Pi\Mvc\Router\Http\Standard;

/**
 * Custom default route class, using for SEO
 * 
 * Example:
 * <CODE>
 * $routeName = '.' . Service::getRouteName();
 * // Article homepage
 * $this->url($routeName, array('controller' => 'article', 'action' => 'index'));
 * // Article all list page, t and p are extra parameters
 * $this->url($routeName, array('list' => 'all', 't' => 20, 'p' => 2));
 * // Article category list page, p is extra parameter
 * $this->url($routeName, array('category' => 'sport', 'p' => 3));
 * // Tag related article list page
 * $this->url($routeName, array('tag' => '标签'));
 * // Article detail page, the value of time field is the article published time
 * $this->url($routeName, array('id' => 3, 'time' => '20130725');
 * // Article detail page with slug
 * $this->url($routeName, array('slug' => '文章', 'time' => '20010101')));
 * // Topic homepage
 * $this->url($routeName, array('topic' => 'music'));
 * // Topic article list page
 * $this->url($routeName, array('topic' => 'sodkf', 'list' => 'all', 'from' => 'my'));
 * </CODE>
 */
class Article extends Standard
{
    const URL_DELIMITER       = '?';
    const KEY_VALUE_DELIMITER = '=';
    const COMBINE_DELIMITER   = '&amp;';
    
    protected $paramDelimiter = '-';
    protected $prefix = '/a';
    
    protected $defaults = array(
        'module'     => 'article',
        'controller' => 'index',
        'action'     => 'index',
    );

    /**
     * Matching url and resolving parameters
     * 
     * @param Request  $request
     * @param int      $pathOffset
     * @return null|\Zend\Mvc\Router\Http\RouteMatch 
     */
    public function match(Request $request, $pathOffset = null)
    {
        $result = $this->canonizePath($request, $pathOffset);
        if (null === $result) {
            return null;
        }
        list($path, $pathLength) = $result;

        $matches = array();
        list($url, $parameter) = explode(self::URL_DELIMITER, $path);
        if (empty($url)) {
            $controller = 'article';
            $action     = 'index';
        } else {
            $urlParams = explode($this->structureDelimiter, $url);
            if ('list' == $urlParams[0]) {
                $controller = 'list';
                $action     = 'all';
            } elseif (preg_match('/^list-/', $urlParams[0])) {
                list($ignored, $category) = explode($this->keyValueDelimiter, $urlParams[0]);
                $controller = 'category';
                $action     = 'list';
                $category   = urldecode($category);
            } elseif (preg_match('/^tag-/', $urlParams[0])) {
                list($ignored, $tag) = explode($this->keyValueDelimiter, $urlParams[0]);
                $tag        = urldecode($tag);
                $controller = 'tag';
                $action     = 'list';
            } elseif (preg_match('/\d{6}/', $urlParams[0])) {
                $controller = 'article';
                $action     = 'detail';
                if (is_numeric($urlParams[1])) {
                    $id     = $urlParams[1];
                } elseif (is_string($urlParams[1])) {
                    $slug   = urldecode($urlParams[1]);
                } else {
                    return null;
                }
            } elseif ('topic' == $urlParams[0]) {
                $controller = 'topic';
                if (preg_match('/^list-/', $urlParams[1])) {
                    list($ignored, $topic) = explode($this->keyValueDelimiter, $urlParams[1]);
                    $action = 'list';
                } else {
                    $topic = $urlParams[1];
                    $action = 'index';
                }
            }
        }
        $matches  = compact('controller', 'action', 'category', 'tag', 'id', 'slug', 'topic');
        
        $params   = array_filter(explode(self::COMBINE_DELIMITER, $parameter));
        foreach ($params as $param) {
            list($key, $value) = explode(self::KEY_VALUE_DELIMITER, $param);
            if (!isset($matches[$key])) {
                $matches[$key] = urldecode($value);
            }
        }

        return new RouteMatch(array_merge($this->defaults, $matches), $pathLength);
    }

    /**
     * Assembling url by passed parameters.
     * 
     * @param array $params
     * @param array $options
     * @return string 
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $url = '';

        $mergedParams = array_merge($this->defaults, $params);
        if (empty($mergedParams)) {
            return $this->prefix;
        }
        
        $controller = $mergedParams['controller'];
        $action     = $mergedParams['action'];
        if ('article' == $controller and 'index' == $action) {
            return $this->prefix;
        }
        unset($mergedParams['controller']);
        unset($mergedParams['action']);
        unset($mergedParams['module']);
        
        if (isset($mergedParams['time']) and is_numeric($mergedParams['time'])) {
            if (isset($mergedParams['slug']) and !is_numeric($mergedParams['slug'])) {
                $url .= $mergedParams['time'] . $this->structureDelimiter . urlencode($mergedParams['slug']);
                unset($mergedParams['slug']);
            } elseif (isset($mergedParams['id']) and is_numeric($mergedParams['id'])) {
                $url .= $mergedParams['time'] . $this->structureDelimiter . $mergedParams['id'];
                unset($mergedParams['id']);
            }
            unset($mergedParams['time']);
        } elseif (isset($mergedParams['list']) and 'all' == $mergedParams['list']) {
            $url .= 'list';
            unset($mergedParams['list']);
        } elseif (isset($mergedParams['category'])) {
            $url .= 'list' . $this->keyValueDelimiter . urlencode($mergedParams['category']);
            unset($mergedParams['category']);
        } elseif (isset($mergedParams['tag'])) {
            $url .= 'tag' . $this->keyValueDelimiter . urlencode($mergedParams['tag']);
            unset($mergedParams['tag']);
        } elseif (isset($mergedParams['topic'])) {
            $url .= 'topic';
            if (isset($mergedParams['list']) and 'all' == $mergedParams['list']) {
                $url .= $this->structureDelimiter . 'list';
                $url .= $this->keyValueDelimiter . $mergedParams['topic'];
                unset($mergedParams['list']);
            } else {
                $url .= $this->structureDelimiter . $mergedParams['topic'];
            }
            unset($mergedParams['topic']);
        }
        
        $parameter = '';
        if (!empty($mergedParams)) {
            foreach ($mergedParams as $key => $value) {
                $parameter .= $key . self::KEY_VALUE_DELIMITER . urlencode($value) . self::COMBINE_DELIMITER;
            }
            $parameter = rtrim($parameter, self::COMBINE_DELIMITER);
            $url .= self::URL_DELIMITER . $parameter;
        }

        return $this->prefix . $this->structureDelimiter . trim($url, $this->structureDelimiter);
    }
}

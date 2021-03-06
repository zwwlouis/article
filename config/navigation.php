<?php
/**
 * Article module navigation config
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Zongshu Lin <zongshu@eefocus.com>
 * @since           1.0
 * @package         Module\Article
 */

return array(
    'meta'  => array(
        'cms'    => array(
            'title'     => _t('Article site navigation'),
            'section'   => 'front',
        ),
    ),
    'item'  => array(
        // Default front navigation
        'front'   => array(
            'article-homepage'  => array(
                'label'         => _t('Article Homepage'),
                'route'         => 'default',
                'controller'    => 'article',
                'action'        => 'index',
            ),
            'dashboard'         => array(
                'label'         => _t('Dashboard'),
                'route'         => 'default',
                'controller'    => 'dashboard',
                'action'        => 'index',
            ),
        ),
        
        // Default admin navigation
        'admin'   => array(
            'config'            => array(
                'label'         => _t('Configuration'),
                'route'         => 'admin',
                'controller'    => 'config',
                'action'        => 'form',
                'resource'      => array(
                    'resource'  => 'config',
                ),
            ),
            'permission'        => array(
                'label'         => _t('Permission'),
                'route'         => 'admin',
                'controller'    => 'permission',
                'resource'      => array(
                    'resource'  => 'permission',
                ),
            ),
            'analysis'          => array(
                'label'         => _t('Statistics'),
                'route'         => 'admin',
                'controller'    => 'statistics',
                'resource'      => array(
                    'resource'  => 'statistics',
                ),
            ),
        ),
        
        // Custom front navigation, need setup at backend
        'cms'     => array(
            'article-homepage'  => array(
                'label'         => _t('Article Homepage'),
                'route'         => 'default',
                'controller'    => 'article',
            ),
            'article'           => array(
                'label'         => _t('All Articles'),
                'route'         => 'default',
                'controller'    => 'article',
                'action'        => 'published',
                'params'        => array(
                    'from'         => 'all',
                ),
            ),
            'draft'             => array(
                'label'         => _t('My Article'),
                'route'         => 'default',
                'controller'    => 'draft',
                'action'        => 'list',
                'params'        => array(
                    'status'       => 1,
                ),
            ),
            'topic'             => array(
                'label'         => _t('Topic'),
                'route'         => 'default',
                'controller'    => 'topic',
                'action'        => 'article-list',
            ),
            'media'             => array(
                'label'         => _t('Media'),
                'route'         => 'default',
                'controller'    => 'media',
                'action'        => 'list',
            ),
            'author'            => array(
                'label'         => _t('Author'),
                'route'         => 'default',
                'controller'    => 'author',
                'action'        => 'list',
            ),
            'category'          => array(
                'label'         => _t('Category'),
                'route'         => 'default',
                'controller'    => 'category',
                'action'        => 'list-category',
            ),
        ),
    ),
);

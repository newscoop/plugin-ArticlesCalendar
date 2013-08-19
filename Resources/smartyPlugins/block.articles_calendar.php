<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */


/**
 * Newscoop articles_calendar block
 *
 * Type:     block
 * Name:     articles_calendar
 * Purpose:  Displays a form for subscribe button
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_articles_calendar($params, $p_content, &$smarty, &$p_repeat)
{
    if (!isset($p_content)) {
        return '';
    }

    $smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    $context = $smarty->getTemplateVars('gimme');

    if (!isset($params['firstDay'])) {
        $params['firstDay'] = 1;
    }

    if (!isset($params['showDayNames'])) {
        $params['showDayNames'] = 'false';
    }

    if (!isset($params['navigation'])) {
        $params['navigation'] = 'true';
    }

    if (!isset($params['earliestMonth'])) {
        $date = new \DateTime();
        $date->modify('-12 months');
        $params['earliestMonth'] = $date->format('Y/m');
    }

    if (!isset($params['latestMonth'])) {
        $params['latestMonth'] = 'current';
    }

    if (!isset($params['imageWidth'])) {
        $params['imageWidth'] = '140px';
    }

    if (!isset($params['date'])) {
        $params['date'] = date('Y/m/d');
    }

    if (!isset($params['view'])) {
        $params['view'] = 'month';  //can be also "widget"
    }

    $html = "
        <div class='js-articles-calndar-widget-container'></div>
        <script type='text/javascript'>
        $.ajax({
            type: 'POST',
            url: '/plugin/articlescalendar/widget',
            data: {
                'firstDay': '".$params['firstDay']."',
                'showDayNames': '".$params['showDayNames']."',
                'earliestMonth': '".$params['earliestMonth']."',
                'latestMonth': '".$params['latestMonth']."',
                'navigation': '".$params['navigation']."',
                'imageWidth': '".$params['imageWidth']."',
                'date': '".$params['date']."',
                'view': '".$params['view']."'
            },
            dataType: 'html',
            success: function(msg){
                $('.js-articles-calndar-widget-container').html(msg);
            }
        });
        </script>";


    return $html;
}

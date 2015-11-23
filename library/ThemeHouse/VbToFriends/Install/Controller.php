<?php

class ThemeHouse_VbToFriends_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/vbulletin-friends-importer-by-th.2835/';

    /**
     *
     * @see ThemeHouse_Install::_getPrerequisites()
     */
    protected function _getPrerequisites()
    {
        return array(
            'ThemeHouse_Friends' => '1'
        );
    } /* END _getPrerequisites */
}
<?php

class ThemeHouse_VbToFriends_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_VbToFriends' => array(
                'importer' => array(
                    'XenForo_Importer_vBulletin',
                    'XenForo_Importer_vBulletin4x'
                ), /* END 'importer' */
            ), /* END 'ThemeHouse_VbToFriends' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassImporter($class, array &$extend)
    {
        $loadClassImporter = new ThemeHouse_VbToFriends_Listener_LoadClass($class, $extend, 'importer');
        $extend = $loadClassImporter->run();
    } /* END loadClassImporter */
}
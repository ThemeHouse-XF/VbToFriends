<?php

/**
 *
 * @see XenForo_Importer_vBulletin4x
 */
class ThemeHouse_VbToFriends_Extend_XenForo_Importer_vBulletin4x extends XFCP_ThemeHouse_VbToFriends_Extend_XenForo_Importer_vBulletin4x
{

    /**
     * Returns the steps that this importer contain
     * @return array
     */
    public function getSteps()
    {
        $steps = parent::getSteps();

        $steps = array_merge($steps,
            array(
                'friends' => array(
                    'title' => new XenForo_Phrase('th_import_friends_vbtofriends'),
                    'depends' => array(
                        'users'
                    )
                )
            ));

        return $steps;
    } /* END getSteps */

    public function stepFriends($start, array $options)
    {
        $options = array_merge(array(
            'limit' => 100,
            'max' => false
        ), $options);

        $sDb = $this->_sourceDb;
        $prefix = $this->_prefix;

        /* @var $model XenForo_Model_Import */
        $model = $this->_importModel;

        if ($options['max'] === false) {
            $options['max'] = $sDb->fetchOne('
				SELECT MAX(userid)
				FROM ' . $prefix . 'userlist
			');
        }

        $friends = $sDb->fetchAll(
            $sDb->limit(
                '
				SELECT *
				FROM ' . $prefix . 'userlist AS userlist
				WHERE userlist.userid > ' . $sDb->quote($start) . '
                    AND userlist.type = \'buddy\' AND userlist.friend IN (\'yes\', \'pending\')
				ORDER BY userlist.userid
			', $options['limit']));
        if (!$friends) {
            return true;
        }

        $next = 0;
        $total = 0;

        XenForo_Db::beginTransaction();

        foreach ($friends as $friend) {
            $next = $friend['userid'];

            $userId = $model->mapUserId($friend['userid'], 0);
            $friendUserId = $model->mapUserId($friend['relationid'], 0);

            if (!$userId || !$friendUserId) {
                continue;
            }

            /* @var $userModel XenForo_Model_User */
            $userModel = XenForo_Model::create('XenForo_Model_User');

            $friendRecord = $userModel->getFriendRecord($userId, $friendUserId);

            $dw = XenForo_DataWriter::create('ThemeHouse_Friends_DataWriter_Friend');
            if ($friendRecord) {
                $dw->setExistingData($friendRecord);
            } else {
                $dw->set('user_id', $userId);
                $dw->set('friend_user_id', $friendUserId);
                $dw->set('message', 'n/a');
            }
            $dw->set('friend_state', ($friend['friend'] == 'pending' ? 'pending' : 'confirmed'));
            $dw->save();

            $total++;
        }

        XenForo_Db::commit();

        $this->_session->incrementStepImportTotal($total);

        return array(
            $next,
            $options,
            $this->_getProgressOutput($next, $options['max'])
        );
    } /* END stepFriends */
}
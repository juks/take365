<?php

namespace app\commands;

use yii;
use yii\console\Controller;
use app\models\User;
use app\models\Story;
use app\models\Media;

class ImportController extends Controller {
    public function actionUsers($truncate = null) {
        if ($truncate) {
		    $connection = Yii::$app->getDb();
    	    $connection->createCommand()->truncateTable('auth_user')->execute();
        }

		$rows = (new \yii\db\Query())
		    ->select('*')
		    ->from('auth_users')
            ->orderBy('id');

		$b = $rows->batch(100);
		$b->db = \Yii::$app->db1;

		foreach ($b as $i => $batchData) {
        	foreach ($batchData as $userData) {
                // Check if already there
                if (User::find()->where(['id_old' => $userData['id']])->one()) {
                    echo 'Skip ' . $userData['id'] . "\n";
                    continue;
                }

        		$user = new User();
        		$user->setScenario('import');

        		$user->setAttributes([
                        'id_old'            => $userData['id'],
        				'username'			=> $userData['login'],
        				'fullname'			=> $userData['fullname'],
        				'email'				=> $userData['email'],
        				'ip_created'		=> $userData['ip_created'],
        				'password'			=> $userData['password'],
        				'description'		=> $userData['description'],
        				'sex'				=> $userData['sex'],
        			]);

        		if (!$user->save()) {
        			echo "--- Failed to save user ---";
        			print_r($userData);
        			print_r($user->attributes);
        			print_r($user->getErrors());
        			die();
        		}
        	}
        }
    }

    public function actionStories($truncate = null) {
        if ($truncate) {
            $connection = Yii::$app->getDb();
    	    $connection->createCommand()->truncateTable('story')->execute();
        }

		$rows = (new \yii\db\Query())
		    ->select('*')
		    ->from('stories')
            ->orderBy('id');

		$b = $rows->batch(100);
		$b->db = \Yii::$app->db1;

		foreach ($b as $i => $batchData) {
        	foreach ($batchData as $storyData) {
                // Check if already there
                if (Story::find()->where(['id_old' => $storyData['id']])->one()) {
                    echo 'Skip ' . $storyData['id'] . "\n";
                    continue;
                }

        		$story = new Story();
        		$story->setScenario('import');

                $creator = User::find()->where(['id_old' => $storyData['user_id']])->one();
                if (!$creator) {
                    echo "Creator not found!\n";
                    print_r($storyData);
                }

        		$story->setAttributes([
                        'id_old'                => $storyData['id'],
        				'created_by'			=> $creator->id,
        				'status'				=> $storyData['status'] ? 0 : 1,
        				'is_deleted'			=> $storyData['is_deleted'],
        				'time_deleted'			=> $storyData['time_deleted'],
        				'is_active'				=> $storyData['is_active'],
        				'time_created'			=> $storyData['time_created'],
        				'time_start'			=> $storyData['time_start'],
        				'time_published'		=> $storyData['time_published'],
        				'media_count'			=> $storyData['media_count'],
        				'title'					=> $storyData['title'],
        				'description'			=> $storyData['description']
        			]);

        		if (!$story->save()) {
        			echo "--- Failed to save story  ---";
        			print_r($storyData);
        			print_r($story->attributes);
        			print_r($story->getErrors());
        			die();
        		}

        	}
        }
    }

    public function actionMedia($targetId = null, $userId = null, $truncate = null) {
		if ($truncate) {
            $connection = Yii::$app->getDb();
    	    $connection->createCommand()->truncateTable('media')->execute();
        }

		$rows = (new \yii\db\Query())
		    ->select('*')
		    ->from('media');

		if ($targetId) $rows->where('target_id = ' . $targetId);
		if ($userId) $rows->where('user_id = ' . $userId);

		$b = $rows->batch(100);
		$b->db = \Yii::$app->db1;

		foreach ($b as $i => $batchData) {
        	foreach ($batchData as $mediaData) {
                // Check if already there
                if (Media::find()->where(['id_old' => $mediaData['id']])->one()) {
                    echo 'Skip' . $mediaData['id'] . "\n";
                    continue;
                }

        		// Skip userpic
        		if ($mediaData['media_type'] == 1) continue;
        	
        		// Userpic (user photo)
        		if ($mediaData['target_type'] == 1) {
        			$target = User::find()->where(['id_old' => $mediaData['target_id']])->one(); //One($mediaData['target_id']);
        			$mediaAlias = 'userpic';
        			$pathAlias = 'userpic';
        		// Story Image
        		} elseif ($mediaData['target_type'] == 2) {
					$target = Story::find()->where(['id_old' => $mediaData['target_id']])->one(); //One($mediaData['target_id']);
					$mediaAlias = 'storyImage';
					$pathAlias = 'story_image';
				// Unsupported Type
        		} else {
        			echo "Invalid target type!";
        			print_r($mediaData);
        			die();
        		}

        		if (!$target) {
        			echo "Target not found!";
        			print_r($mediaData);
        			die();
        		}

        		$path = Yii::$app->params['mediaImportPath'] . '/' . $mediaData['path_partition'] . '/' . $pathAlias . '/' . $mediaData['path'] . '/' . $mediaData['filename'] . '.' . $mediaData['ext'];
        		$url = 'http://take365.org/media/' . $mediaData['path_partition'] . '/' . $pathAlias . '/' . $mediaData['path'] . '/' . $mediaData['filename'] . '.' . $mediaData['ext'];

        		echo $path . "\n";

        		$media = new Media();
                $media->setScenario('import');

       			$media->setAttributes([
                    'id_old'                => $mediaData['id'],
        			'date'					=> $mediaData['calendar_date'],
        			'time_created'			=> $mediaData['time_created'],
        			'title'					=> $mediaData['title'],
        			'description'			=> $mediaData['description'],
        			'created_by'			=> $mediaData['user_id'],
        		]);

       			try {
	       			if (file_exists($path)) {
	        			$media = $target->addMedia($path, $mediaAlias, $media);
	        		} else {
	        			echo 'Local File not found. Downloading ' . $url . "\n";
	        			$media = $target->addMedia($url, $mediaAlias, $media);
	        		}
        		} catch (\Exception $e) {
                    echo "---------------------------------\n";
        			echo "Warning: failed to load file!\n";
                    echo $e->getMessage();
                    echo "---------------------------------\n\n";

        			continue;
        		}
        	}
        }
    }
}

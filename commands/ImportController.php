<?php

namespace app\commands;

use yii;
use yii\console\Controller;
use app\models\User;
use app\models\Story;
use app\models\Media;

class ImportController extends Controller {
    public $skipFiles;
    public $truncate;
    public $targetId;
    public $userId;
    public $mediaType;

    public function options($actionId) {
        $o = [
                'users'     => ['truncate'],
                'stories'   => ['truncate'],
                'media'     => ['truncate', 'targetId', 'userId', 'mediaType', 'skipFiles']
            ];

        if (!empty($o[$actionId])) return $o[$actionId]; else return null;
    }

    public function actionUsers() {
        if ($this->truncate) {
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
                        'homepage'          => $userData['url'],
        				'ip_created'		=> $userData['ip_created'],
        				'password'			=> $userData['password'],
        				'description'		=> $userData['description'],
                        'description_jvx'   => $userData['description_jvx'],
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

    public function actionStories() {
        if ($this->truncate) {
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
        				'description'			=> $storyData['description'],
                        'description_jvx'       => $storyData['description_jvx'],
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

    public function actionMedia() {
		if ($this->truncate) {
            $connection = Yii::$app->getDb();
    	    $connection->createCommand()->truncateTable('media')->execute();
        }

		$rows = (new \yii\db\Query())
		    ->select('*')
		    ->from('media');

		if ($this->targetId) $rows->where('target_id = ' . $this->targetId);
		if ($this->userId) $rows->where('user_id = ' . $this->userId);
        if ($this->mediaType) $rows->where('media_type = ' . $this->mediaType);

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
                    $downloadAlias = 'user_photo';
        		// Story Image
        		} elseif ($mediaData['target_type'] == 2) {
					$target = Story::find()->where(['id_old' => $mediaData['target_id']])->one(); //One($mediaData['target_id']);
					$mediaAlias = 'storyImage';
					$pathAlias = 'story_image';
                    $downloadAlias = 'storyImage';
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
        		$url = 'http://take365.org/media/' . $mediaData['path_partition'] . '/' . $downloadAlias . '/' . $mediaData['path'] . '/' . $mediaData['filename'] . '.' . $mediaData['ext'];

        		if (!$this->skipFiles) echo $path . "\n";

        		$media = new Media();
                $media->setScenario('import');

       			$media->setAttributes([
                    'id_old'                => $mediaData['id'],
        			'date'					=> $mediaData['calendar_date'],
        			'time_created'			=> $mediaData['time_created'],
        			'title'					=> $mediaData['title'],
        			'description'			=> $mediaData['description'],
                    'description_jvx'       => $mediaData['description_jvx'],
        			'created_by'			=> $mediaData['user_id'],
        		]);

                if ($this->skipFiles) {
                    $media->setAttributes([
                                                'target_id'     => $target->id,
                                                'target_type'   => $target->getType(),
                                                'filename'      => 'empty',
                                                'ext'           => 'emp',
                                                'type'          => Media::getTypeByAlias($mediaAlias)
                                            ]);

                    if (!$media->save()) {
                        echo "--- Failed to save media ---";
                        print_r($media->getErrors());

                        die();
                    }

                    continue;
                }

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

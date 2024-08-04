<?php

namespace ls\tests;

/**
 * @group api
 */
class ErrorLoggingTest extends TestBaseClassWeb
{
    public function testErrorLogFileIsCreatedOnError()
    {
            $urlMan = \Yii::app()->urlManager;
            $urlMan->setBaseUrl('http://' . self::$domain . '/index.php');
            $url = $urlMan->createUrl(
                'absolutelyNonExistingUrlThatCreatesAnErrorLog',
            );
            $web = self::$webDriver;
            $web->get($url);
            sleep(1);
            $this->assertFileExists(self::$errorLogFileName);
            unlink(self::$errorLogFileName);
        }

}

<?php

/**
 * @todo Apply new permission 'extensions' instead of 'settings'.
 */
class PluginManagerController extends Survey_Common_Action
{
    public function init()
    {
    }

    /**
     * Overview for plugins
     * Copied from PluginsController 2015-10-02
     */
    public function index()
    {
        $jsFile = App()->getConfig('adminscripts') . 'plugin_manager.js';
        App()->getClientScript()->registerScriptFile($jsFile);

        $aoPlugins = Plugin::model()->findAll(array('order' => 'name'));
        $data      = [];
        foreach ($aoPlugins as $oPlugin) {
            $data[] = [
                'id'          => $oPlugin->id,
                'name'        => $oPlugin->name,
                'load_error'  => $oPlugin->load_error,
                'description' => '',
                'active'      => $oPlugin->active,
                'settings'    => []
            ];
        }

        if (Yii::app()->request->getParam('pageSize')) {
            Yii::app()->user->setState('pageSize', intval(Yii::app()->request->getParam('pageSize')));
        }

        $aData['fullpagebar']['returnbutton']['url'] = 'index';
        $aData['fullpagebar']['returnbutton']['text'] = gT('Return to admin home');
        $aData['data'] = $data;
        $aData['plugins'] = $aoPlugins;
        $aData['scanFilesUrl'] = $this->getController()->createUrl(
            '/admin/pluginmanager',
            [
                'sa' => 'scanFiles',
            ]
        );

        if (!Permission::model()->hasGlobalPermission('settings', 'read')) {
            Yii::app()->setFlashMessage(gT("No permission"), 'error');
            $this->getController()->redirect(array('/admin'));
        }
        $this->_renderWrappedTemplate('pluginmanager', 'index', $aData);
    }

    /**
     * Scan files in plugin folder and add them to the database.
     * @return void
     */
    public function scanFiles()
    {
        if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
            Yii::app()->setFlashMessage(gT('No permission'), 'error');
            $this->getController()->redirect($this->getPluginManagerUrl());
        }

        $oPluginManager = App()->getPluginManager();
        $result = $oPluginManager->scanPlugins();

        Yii::app()->setFlashMessage(
            sprintf(
                gT('Found %s plugins in file system'),
                count($result)
            ),
            'notice'
        );

        $data = [];
        $data['result'] = $result;
        $data['installUrl'] = $this->getController()->createUrl(
            '/admin/pluginmanager',
            [
                'sa' => 'installPluginFromFile'
            ]
        );
        $data['fullpagebar']['returnbutton']['url'] = 'pluginmanager';
        $data['fullpagebar']['returnbutton']['text'] = gT('Return to plugin manager');

        $this->_renderWrappedTemplate(
            'pluginmanager',
            'scanFilesResult',
            $data
        );

        //$indexUrl = $this->getController()->createUrl('/admin/pluginmanager');
        //$this->getController()->redirect($indexUrl);
    }

    /**
     * Activate a plugin
     *
     * @todo Defensive programming
     * @param int $id Plugin id
     * @return void
     */
    public function activate()
    {
        if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
            Yii::app()->setFlashMessage(gT('No permission'), 'error');
            $this->getController()->redirect($this->getPluginManagerUrl());
        }

        $request = Yii::app()->request;
        $pluginId = (int) $request->getPost('pluginId');

        $oPlugin = Plugin::model()->findByPk($pluginId);
        if ($oPlugin && $oPlugin->active == 0) {
            // Load the plugin:
            App()->getPluginManager()->loadPlugin($oPlugin->name, $pluginId);
            $result = App()->getPluginManager()->dispatchEvent(
                new PluginEvent('beforeActivate', $this),
                $oPlugin->name
            );
            if ($result->get('success', true)) {
                $oPlugin->active = 1;
                $oPlugin->save();
                Yii::app()->user->setFlash('success', gT('Plugin was activated.'));
            } else {
                $customMessage = $result->get('message');
                if ($customMessage) {
                    Yii::app()->user->setFlash('error', $customMessage);
                } else {
                    Yii::app()->user->setFlash('error', gT('Failed to activate the plugin.'));
                }
                $this->getController()->redirect(array('admin/pluginmanager/sa/index/'));
            }
        } else {
            Yii::app()->user->setFlash('error', gT('Found no plugin, or plugin already active.'));
        }
        $this->getController()->redirect(array('admin/pluginmanager/sa/index/'));
    }

    /**
     * Deactivate plugin.
     * @return void
     */
    public function deactivate()
    {
        if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
            Yii::app()->setFlashMessage(gT("No permission"), 'error');
            $this->getController()->redirect(array('/admin/pluginmanager/sa/index'));
        }
        $pluginId = (int) Yii::app()->request->getPost('pluginId');
        $plugin = Plugin::model()->findByPk($pluginId);
        if ($plugin && $plugin->active) {
            $result = App()->getPluginManager()->dispatchEvent(
                new PluginEvent('beforeDeactivate', $this),
                $plugin->name
            );
            if ($result->get('success', true)) {
                $plugin->active = 0;
                $plugin->save();
                Yii::app()->user->setFlash('success', gT('Plugin was deactivated.'));
            } else {
                $customMessage = $result->get('message');
                if ($customMessage) {
                    Yii::app()->user->setFlash('error', $customMessage);
                } else {
                    Yii::app()->user->setFlash('error', gT('Failed to deactivate the plugin.'));
                }
                $this->getController()->redirect($this->getPluginManagerUrl());
            }
        } else {
            Yii::app()->user->setFlash('error', gT('Found no plugin, or plugin not active.'));
        }

        $this->getController()->redirect($this->getPluginManagerUrl());
    }

    /**
     * Configure for plugin
     */
    public function configure($id)
    {
        $url = $this->getController()->createUrl(
            '/admin/pluginmanager',
            [
                'sa' => 'index'
            ]
        );
        if (!Permission::model()->hasGlobalPermission('settings', 'read')) {
            Yii::app()->setFlashMessage(gT("No permission"), 'error');
            $this->getController()->redirect($url);
        }

        $plugin      = Plugin::model()->findByPk($id);
        $oPluginObject = App()->getPluginManager()->loadPlugin($plugin->name, $plugin->id);

        if (!$oPluginObject->readConfigFile()) {
            Yii::app()->user->setFlash('error', gT('Found no configuration file for this plugin.'));
            $this->getController()->redirect($url);
        }

        if ($plugin === null) {
            Yii::app()->user->setFlash('error', gT('The plugin was not found.'));
            $this->getController()->redirect($url);
        }

        // If post handle data, yt0 seems to be the submit button
        // TODO: Break out to separate method.
        if (App()->request->isPostRequest) {
            if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
                Yii::app()->setFlashMessage(gT("No permission"), 'error');
                $this->getController()->redirect($url);
            }
            $aSettings = $oPluginObject->getPluginSettings(false);
            $aSave     = array();
            foreach (array_keys($aSettings) as $name) {
                $aSave[$name] = App()->request->getPost($name, null);
            }
            $oPluginObject->saveSettings($aSave);
            Yii::app()->user->setFlash('success', gT('The plugin settings were saved.'));
            if (App()->request->getPost('redirect')) {
                $this->getController()->redirect(App()->request->getPost('redirect'), true);
            }
        }

        // Prepare settings to be send to the view.
        $aSettings = $oPluginObject->getPluginSettings();

        // Send to view plugin porperties: name and description
        $aPluginProp = App()->getPluginManager()->getPluginInfo($plugin->name);

        $fullPageBar = [];
        $fullPageBar['returnbutton']['url'] = 'admin/pluginmanager/sa/index';
        $fullPageBar['returnbutton']['text'] = gT('Return to plugin list');

        $this->_renderWrappedTemplate(
            'pluginmanager',
            'configure',
            [
                'settings'     => $aSettings,
                'plugin'       => $plugin,
                'pluginObject' => $oPluginObject,
                'properties'   => $aPluginProp,
                'fullpagebar'  => $fullPageBar
            ]
        );
    }

    /**
     * Set load_error to 0 for plugin with id $pluginId.
     * This makes it possible to try to load the plugin again,
     * if a fix for previous load error has been implemented.
     *
     * @param int $pluginId
     * @return void
     */
    public function resetLoadError($pluginId)
    {
        $url = $this->getController()->createUrl(
            '/admin/pluginmanager',
            [
                'sa' => 'index'
            ]
        );

        if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
            Yii::app()->setFlashMessage(gT('No permission'), 'error');
            $this->getController()->redirect($url);
        }

        $pluginId = (int) $pluginId;
        $plugin = Plugin::model()->find('id = :id', [':id' => $pluginId]);
        if ($plugin) {
            $plugin->load_error = 0;
            $plugin->load_error_message = '';
            $result = $plugin->update();
            if ($result) {
                Yii::app()->user->setFlash('success', sprintf(gt('Reset load error for plugin %d'), $pluginId));
            } else {
                Yii::app()->user->setFlash('error', sprintf(gt('Could not update plugin %d'), $pluginId));
            }
            $this->getController()->redirect($url);
        } else {
            Yii::app()->user->setFlash('error', sprintf(gt('Found no plugin with id %d'), $pluginId));
            $this->getController()->redirect($url);
        }
    }

    /**
     * Install a plugin that has been discovered in the file system.
     * @return void
     */
    public function installPluginFromFile()
    {
        // Check permissions.
        if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
            Yii::app()->setFlashMessage(gT('No permission'), 'error');
            $this->getController()->redirect($this->getPluginManagerUrl());
        }

        $request = Yii::app()->request;
        $pluginName = $request->getPost('pluginName');

        $pluginManager = App()->getPluginManager();
        $pluginInfo = $pluginManager->getPluginInfo($pluginName);

        if (empty($pluginInfo)) {
            Yii::app()->setFlashMessage(
                sprintf(
                    gT('Found no plugin with name %s'),
                    json_encode($pluginName)  // json_encode in case of null.
                ),
                'error'
            );
            $this->getController()->redirect($this->getPluginManagerUrl());
        } else {
            list($result, $errorMessage) = $pluginManager->installPlugin(
                $pluginInfo['pluginConfig'],
                $pluginInfo['pluginType']
            );
            if ($result) {
                Yii::app()->setFlashMessage(
                    gT('Plugin was installed.'),
                    'success'
                );
            } else {
                Yii::app()->setFlashMessage(
                    $errorMessage,
                    'error'
                );
            }
        }
        $this->getController()->redirect($this->getPluginManagerUrl());
    }

    /**
     * Run when user click button to uninstall plugin.
     * @return void
     */
    public function uninstallPlugin()
    {
        // Check permissions.
        if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
            Yii::app()->setFlashMessage(gT('No permission'), 'error');
            $this->getController()->redirect($this->getPluginManagerUrl());
        }

        // Get plugin id from post.
        $request = Yii::app()->request;
        $pluginId = (int) $request->getPost('pluginId');

        $plugin = Plugin::model()->find('id = :id', [':id' => $pluginId]);

        // Check if plugin exists.
        if (empty($plugin)) {
            Yii::app()->setFlashMessage(
                sprintf(
                    gT('Found no plugin with id %d.'),
                    $pluginId
                ),
                'error'
            );
            $this->getController()->redirect($this->getPluginManagerUrl());
        } else {
            if ($plugin->delete()) {
                Yii::app()->setFlashMessage(gT('Plugin uninstalled.'), 'success');
            } else {
                Yii::app()->setFlashMessage(gT('Could not uninstall plugin.'), 'error');
            }
            $this->getController()->redirect($this->getPluginManagerUrl());
        }
    }

    /**
     * Upload a plugin ZIP file.
     * @return void
     */
    public function upload()
    {
        // Check permissions.
        if (!Permission::model()->hasGlobalPermission('settings', 'update')) {
            Yii::app()->setFlashMessage(gT('No permission'), 'error');
            $this->getController()->redirect($this->getPluginManagerUrl());
        }

        Yii::app()->loadLibrary('admin.pclzip');
        $pluginManager = App()->getPluginManager();

        // Redirect back if demo mode is set.
        $this->checkDemoMode();

        // Redirect back at file size error.
        $this->checkFileSizeError();

        $sNewDirectoryName = sanitize_dirname(pathinfo($_FILES['the_file']['name'], PATHINFO_FILENAME));
        // TODO: Customize folders from config.php?
        $uploadDir = Yii::getPathOfAlias($pluginManager->pluginDirs['upload']);
        $destdir = $uploadDir . DIRECTORY_SEPARATOR . $sNewDirectoryName;

        // Redirect back if $destdir is not writable OR if it already exists.
        $this->checkDestDir($destdir, $sNewDirectoryName);

        // All OK if we're here.
        mkdir($destdir);

        $this->extractZipFile($destdir);
    }

    /**
     * @param string $destdir
     * @return void
     */
    protected function extractZipFile($destdir)
    {
        if (!is_file($_FILES['the_file']['tmp_name'])) {
            Yii::app()->setFlashMessage(
                gT("An error occurred uploading your file. This may be caused by incorrect permissions for the application /tmp folder."),
                'error'
            );
            rmdirr($destdir);
            $this->getController()->redirect($this->getPluginManagerUrl());
        }

        $zip = new PclZip($_FILES['the_file']['tmp_name']);
        $aExtractResult = $zip->extract(
            PCLZIP_OPT_PATH,
            $destdir,
            PCLZIP_CB_PRE_EXTRACT,
            'pluginExtractFilter'
        );

        if ($aExtractResult === 0) {
            Yii::app()->user->setFlash(
                'error',
                gT("This file is not a valid ZIP file archive. Import failed.")
                . ' ' . $zip->error_string
            );
            rmdirr($destdir);
            $this->getController()->redirect($this->getPluginManagerUrl());
        } else {
            $pluginManager = App()->getPluginManager();
            list($result, $errorMessage) = $pluginManager->installUploadedPlugin($destdir);
            Yii::app()->user->setFlash(
                'success',
                gT('The plugin was successfully uploaded.')
            );
        }
        $this->getController()->redirect($this->getPluginManagerUrl());
    }

    /**
     * Redirect back if $destdir is not writable or already exists.
     * @param string $destdir
     * @param string $sNewDirectoryName
     * @return void
     * @todo Duplicate from themes.php.
     */
    protected function checkDestDir($destdir, $sNewDirectoryName)
    {
        if (!is_writeable(dirname($destdir))) {
            Yii::app()->user->setFlash(
                'error',
                sprintf(
                    gT("Incorrect permissions in your %s folder."),
                    dirname($destdir)
                )
            );
            $this->getController()->redirect($this->getPluginManagerUrl());
        }

        if (is_dir($destdir)) {
            Yii::app()->user->setFlash(
                'error',
                sprintf(
                    gT("Plugin '%s' does already exist."),
                    $sNewDirectoryName
                )
            );
            $this->getController()->redirect($this->getPluginManagerUrl());
        }
    }

    /**
     * Redirects if demo mode is set.
     * @return void
     * @todo Duplicate from themes.php.
     */
    protected function checkDemoMode()
    {
        if (Yii::app()->getConfig('demoMode')) {
            Yii::app()->user->setFlash('error', gT("Demo mode: Uploading plugins is disabled."));
            $this->getController()->redirect($this->getPluginManagerUrl());
        }
    }

    /**
     * Redirect if file size is too big.
     * @return void
     * @todo Duplicate from themes.php.
     */
    protected function checkFileSizeError()
    {
        if ($_FILES['the_file']['error'] == 1 || $_FILES['the_file']['error'] == 2) {
            Yii::app()->setFlashMessage(
                sprintf(
                    gT("Sorry, this file is too large. Only files up to %01.2f MB are allowed."),
                    getMaximumFileUploadSize() / 1024 / 1024
                ),
                'error'
            );
            $this->getController()->redirect($this->getPluginManagerUrl());
        }
    }


    /**
     * Return URL to plugin manager index..
     * @return string
     */
    protected function getPluginManagerUrl()
    {
        return $this->getController()->createUrl(
            '/admin/pluginmanager',
            [
                'sa' => 'index'
            ]
        );
    }

    /**
     * Renders template(s) wrapped in header and footer
     *
     * @param string $sAction Current action, the folder to fetch views from
     * @param string $aViewUrls View url(s)
     * @param array $aData Data to be passed on. Optional.
     */
    protected function _renderWrappedTemplate($sAction = 'pluginmanager', $aViewUrls = [], $aData = [], $sRenderFile = false)
    {
        parent::_renderWrappedTemplate($sAction, $aViewUrls, $aData, $sRenderFile);
    }
}

/**
 * PCLZip callback for plugin ZIP install.
 * @param mixed $p_event
 * @param mixed $p_header
 * @return int Return 1 for yes (file can be extracted), 0 for no
 */
function pluginExtractFilter($p_event, &$p_header)
{
    $aAllowExtensions = explode(
        ',',
        Yii::app()->getConfig('allowedpluginuploads')
    );
    $info = pathinfo($p_header['filename']);
    // Deny files with multiple extensions in general
    if (substr_count($info['basename'], '.') > 1) {
        return 0;
    }

    if ($p_header['folder']
        || !isset($info['extension'])
        || in_array($info['extension'], $aAllowExtensions)) {
        return 1;
    } else {
        return 0;
    }
}

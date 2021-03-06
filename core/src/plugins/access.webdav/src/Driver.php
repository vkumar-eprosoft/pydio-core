<?php
/*
 * Copyright 2007-2013 Charles du Jeu - Abstrium SAS <team (at) pyd.io>
 * This file is part of Pydio.
 *
 * Pydio is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pydio is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Pydio.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The latest code can be found at <http://pyd.io/>.
 *
 */

namespace Pydio\Access\WebDAV;

defined('AJXP_EXEC') or die( 'Access not allowed');

use AJXP_MetaStreamWrapper;
use Pydio\Access\Core\Stream\StreamWrapper as AccessStreamWrapper;
use ConfService;
use RecycleBinManager;

/**
 * AJXP_Plugin to access a webdav enabled server
 * @package AjaXplorer_Plugins
 * @subpackage Access
 */
class Driver extends \fsAccessDriver
{
    /**
    * @var Repository
    */
    public $repository;
    public $driverConf;
    protected $client;
    protected $wrapperClassName;
    protected $urlBase;

    /*
     * Driver Initialization
     *
     */
    public function init($repository, $options = array())
    {
        parent::init($repository, $options);

        AJXP_MetaStreamWrapper::appendMetaWrapper("auth.dav", "Pydio\Access\Core\Stream\AuthWrapper", "pydio.dav");
        AJXP_MetaStreamWrapper::appendMetaWrapper("path.dav", "Pydio\Access\Core\Stream\PathWrapper", "pydio.dav");
    }

    /**
     * Repository Initialization
     *
     */
    public function initRepository()
    {
        $this->detectStreamWrapper(true);

        if (is_array($this->pluginConf)) {
            $this->driverConf = $this->pluginConf;
        } else {
            $this->driverConf = array();
        }

        $client = new Client([
            'base_url' => $this->repository->getOption("HOST")
        ]);

        // Params
        $recycle = $this->repository->getOption("RECYCLE_BIN");

        // Config
        ConfService::setConf("PROBE_REAL_SIZE", false);
        $this->urlBase = "pydio://".$this->repository->getId();
        if ($recycle != "") {
            RecycleBinManager::init($this->urlBase, "/".$recycle);
        }
    }

}

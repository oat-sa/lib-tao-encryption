<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\taoEncryption\scripts\update;

use common_ext_ExtensionUpdater;
use core_kernel_users_GenerisUser;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoEncryption\Service\KeyProvider\KeyProviderClient;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoEncryption\Service\KeyProvider\SimpleKeyProviderService;
use oat\taoEncryption\Service\User\EncryptedUserFactoryService;

class Updater extends common_ext_ExtensionUpdater
{
    /**
     * @param $initialVersion
     * @return string|void
     * @throws \Exception
     */
    public function update($initialVersion)
    {
        $this->skip('0.1.0', '0.4.0');

        if ($this->isVersion('0.4.0')) {
            OntologyUpdater::syncModels();
            $this->getServiceManager()->register(KeyProviderClient::SERVICE_ID, new KeyProviderClient());
            AclProxy::applyRule(
                new AccessRule(
                    AccessRule::GRANT,
                    'http://www.tao.lu/Ontologies/generis.rdf#EncryptionRole',
                    array('ext'=>'taoEncryption', 'mod' => 'EncryptionApi')
                )
            );
            $this->setVersion('0.5.0');
        }

        if ($this->isVersion('0.5.0')){
            $simpleKeyProvider = new SimpleKeyProviderService([]);

            $this->getServiceManager()->register(SimpleKeyProviderService::SERVICE_ID, $simpleKeyProvider);

            $fileKeyProvider = new FileKeyProviderService([
                FileKeyProviderService::OPTION_FILESYSTEM_ID => 'keysEncryption'
            ]);

            $this->getServiceManager()->register(FileKeyProviderService::SERVICE_ID, $fileKeyProvider);

            $this->setVersion('0.6.0');
        }

        if ($this->isVersion('0.6.0')){
            $userFactory = $this->getServiceManager()->get(EncryptedUserFactoryService::SERVICE_ID);
            $userFactory->setOption(EncryptedUserFactoryService::OPTION_USER_CLASS_WRAPPED, core_kernel_users_GenerisUser::class);

            $this->getServiceManager()->register(EncryptedUserFactoryService::SERVICE_ID, $userFactory);
            $this->setVersion('0.6.1');
        }

        $this->skip('0.6.1', '0.8.1');

        if ($this->isVersion('0.8.1')){
            AclProxy::applyRule(
                new AccessRule(
                    AccessRule::GRANT,
                    'http://www.tao.lu/Ontologies/generis.rdf#EncryptionRole',
                    array('ext'=>'taoEncryption', 'mod' => 'DecryptResultsAction')
                )
            );
            $this->setVersion('0.9.0');
        }
    }
}
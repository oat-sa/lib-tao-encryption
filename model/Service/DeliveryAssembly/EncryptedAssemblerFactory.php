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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoEncryption\Service\DeliveryAssembly;

use oat\oatbox\service\ConfigurableService;
use oat\taoDeliveryRdf\model\AssemblerServiceInterface;
use oat\taoEncryption\Service\EncryptionAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class EncryptedAssemblerFactory extends ConfigurableService
{
    use ServiceLocatorAwareTrait;

    /**
     * @return AssemblerServiceInterface|EncryptionAwareInterface
     */
    public function create()
    {
        $assembler = $this->getServiceLocator()->get(AssemblerServiceInterface::SERVICE_ID);
        $assemblerOptions = $assembler->getOptions();

        $encryptedAssembler = new EncryptedAssemblerService($assemblerOptions);
        $encryptedAssembler->setServiceLocator($this->getServiceLocator());

        return $encryptedAssembler;
    }
}

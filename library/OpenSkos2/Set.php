<?php

/**
 * OpenSKOS
 *
 * LICENSE
 *
 * This source file is subject to the GPLv3 license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @category   OpenSKOS
 * @package    OpenSKOS
 * @copyright  Copyright (c) 2015 Picturae (http://www.picturae.com)
 * @author     Picturae
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 */
namespace OpenSkos2;

use OpenSkos2\Rdf\Resource;
use OpenSkos2\Namespaces\Dcmi;
use OpenSkos2\Namespaces\DcTerms;
use OpenSkos2\Namespaces\Rdf;
use OpenSkos2\Namespaces\OpenSkos;
use OpenSkos2\Rdf\Uri;

class Set extends Resource
{
    const TYPE = Dcmi::DATASET;
    
   
     public function __construct($uri = null)
    {
        parent::__construct($uri);
        $this->addProperty(Rdf::TYPE, new Uri(self::TYPE));
    }
    
    public function getTenantUri(){
        $tenants = $this ->getProperty(DcTerms::PUBLISHER);
        if (count($tenants) < 1) {
            return null;
        } else {
            return $tenants[0];
        }
    }
    
    public function addMetadata($userUri, $params, $existingSet) {
        $metadata = [];
        if (count($this->getProperty(DcTerms::PUBLISHER)) < 1) {
                $metadata = [DcTerms::PUBLISHER => new Uri($params['tenanturi'])];
        }
        if ($existingSet !== null) {
            if (count($this->getProperty(OpenSkos::UUID)) < 1) {
                $metadata = [OpenSkos::UUID => $existingSet->getUuid()];
            }
        }
        foreach ($metadata as $property => $defaultValue) {
                $this->setProperty($property, $defaultValue);
        }
    }
}

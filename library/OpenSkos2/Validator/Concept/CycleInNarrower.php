<?php

/*
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

namespace OpenSkos2\Validator\Concept;

use OpenSkos2\Concept;
use OpenSkos2\Namespaces\Skos;
use OpenSkos2\Rdf\Serializer\NTriple;
use OpenSkos2\Rdf\Uri;
use OpenSkos2\Validator\AbstractConceptValidator;

class CycleInNarrower extends AbstractConceptValidator
{

    /**
     * Validate if a concept will make a cyclic relationship, this is supported by SKOS
     * but was not supported in OpenSKOS this validator provides a way to restrict it in a similar way
     *
     * @param Concept $concept
     * @return bool
     */
    protected function validateConcept(Concept $concept)
    {
        $narrowerTerms = $concept->getProperty(Skos::NARROWER);

        if (empty($narrowerTerms)) {
            return true;
        }

        $uri = new Uri($concept->getUri());

        $query = '?narrower skos:narrower+ ' . (new NTriple())->serialize($uri) . PHP_EOL
            . ' FILTER(?narrower IN (' . (new NTriple())->serializeArray($narrowerTerms) . '))';
        if ($this->resourceManager->ask($query)) {
            $this->errorMessages[] = "Cyclic narrower relation detected for concept: {$concept->getUri()}";

            return false;
        }

        return true;
    }
}

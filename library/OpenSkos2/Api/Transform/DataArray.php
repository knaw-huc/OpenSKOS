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

namespace OpenSkos2\Api\Transform;

use OpenSkos2\Namespaces\DcTerms;
use OpenSkos2\Namespaces\Skos;
use OpenSkos2\Namespaces\OpenSkos;
use OpenSkos2\FieldsMaps;

/**
 * Transform \OpenSkos2\Concept to a php array with only native values to encode as json output.
 * Provide backwards compatability to the API output from OpenSKOS 1 as much as possible
 */
class DataArray
{
    /**
     * @var \OpenSkos2\Concept
     */
    private $concept;
    
    /**
     * @var array
     */
    private $propertiesList;
    
    /**
     * @param \OpenSkos2\Concept $concept
     * @param array $propertiesList Properties to serialize.
     */
    public function __construct(\OpenSkos2\Concept $concept, $propertiesList = null)
    {
        $this->concept = $concept;
        $this->propertiesList = $propertiesList;
    }
    
    /**
     * Transform the
     *
     * @return array
     */
    public function transform()
    {
        $concept = $this->concept;
        
        /* @var $concept \OpenSkos2\Concept */
        $newConcept = [];
        if ($this->doIncludeProperty('uri')) {
            $newConcept['uri'] = $concept->getUri();
        }
        
        foreach (self::getFieldsPlusIsRepeatableMap() as $field => $prop) {
            if (!$this->doIncludeProperty($prop['uri'])) {
                continue;
            }
            
            $data = $concept->getProperty($prop['uri']);
            if (empty($data)) {
                continue;
            }
            $newConcept = $this->getPropertyValue($data, $field, $prop, $newConcept);
        }
        return $newConcept;
    }
    
    /**
     * Should the property be included in the serialized data.
     * @param string $property
     * @return bool
     */
    protected function doIncludeProperty($property)
    {
        return empty($this->propertiesList) || in_array($property, $this->propertiesList);
    }
    
    /**
     * Get data from property
     *
     * @param array $prop
     * @param array $settings
     * #param string $field field name to map
     * @param array $concept
     * @return array
     */
    private function getPropertyValue(array $prop, $field, $settings, $concept)
    {
        foreach ($prop as $val) {
            // Some values only have a URI but not getValue or getLanguage
            if ($val instanceof \OpenSkos2\Rdf\Uri && !method_exists($val, 'getLanguage')) {
                if ($settings['repeatable'] === true) {
                    $concept[$field][] = $val->getUri();
                } else {
                    $concept[$field] = $val->getUri();
                }
                continue;
            }

            $value = $val->getValue();

            if ($value instanceof \DateTime) {
                $value = $value->format(DATE_W3C);
            }

            if (empty($value)) {
                continue;
            }
            $lang = $val->getLanguage();
            $langField = $field;
            if (!empty($lang)) {
                $langField .= '@' . $lang;
            }
            
            if ($settings['repeatable'] === true) {
                $concept[$langField][] = $value;
            } else {
                $concept[$langField] = $value;
            }
        }
        
        return $concept;
    }
    
    /**
     * Gets map of fields to properties. Including info for if a field is repeatable.
     * @return array
     */
    public static function getFieldsPlusIsRepeatableMap()
    {
        $notRepeatable = [
            DcTerms::CREATOR,
            DcTerms::DATESUBMITTED,
            DcTerms::DATEACCEPTED,
            DcTerms::MODIFIED,
            OpenSkos::ACCEPTEDBY,
            OpenSkos::STATUS,
            OpenSkos::TENANT,
            OpenSkos::SET,
            OpenSkos::UUID,
            OpenSkos::TOBECHECKED,
            Skos::PREFLABEL,
        ];
        
        $map = [];
        foreach (FieldsMaps::getOldToProperties() as $field => $property) {
            $map[$field] = [
                'uri' => $property,
                'repeatable' => !in_array($property, $notRepeatable),
            ];
        }
        
        return $map;
    }
}

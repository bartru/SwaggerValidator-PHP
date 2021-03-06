<?php

/*
 * Copyright 2016 Nicolas JUHEL <swaggervalidator@nabbar.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace SwaggerValidator\Object;

/**
 * Description of Parameters
 *
 * @author Nicolas JUHEL<swaggervalidator@nabbar.com>
 * @version 1.0.0
 */
class Parameters extends \SwaggerValidator\Common\CollectionSwagger
{

    public function __construct()
    {

    }

    /**
     * Var Export Method
     */
    protected function __storeData($key, $value = null)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
        else {
            parent::__storeData($key, $value);
        }
    }

    public static function __set_state(array $properties)
    {
        $obj = new static;

        foreach ($properties as $key => $value) {
            $obj->__storeData($key, $value);
        }

        return $obj;
    }

    public function jsonUnSerialize(\SwaggerValidator\Common\Context $context, $jsonData)
    {
        $this->checkJsonObjectOrArray($context, $jsonData);

        if (is_object($jsonData)) {
            $jsonData = get_object_vars($jsonData);
        }

        $keyIn = \SwaggerValidator\Common\FactorySwagger::KEY_IN;

        foreach ($jsonData as $key => $value) {
            $value = $this->extractNonRecursiveReference($context, $value);

            if (!property_exists($value, $keyIn)) {
                $context->throwException('Parameters "' . $key . '" is not well defined !', __METHOD__, __LINE__);
            }

            $this->$key = \SwaggerValidator\Common\FactorySwagger::getInstance()->jsonUnSerialize($context->setDataPath($key), $this->getCleanClass(__CLASS__), $key, $value);
        }

        $context->logDecode(get_class($this), __METHOD__, __LINE__);
    }

    public function validate(\SwaggerValidator\Common\Context $context)
    {
        if ($context->getType() !== \SwaggerValidator\Common\Context::TYPE_REQUEST) {
            return true;
        }

        $check = true;

        foreach ($this->keys() as $key) {
            if (is_object($this->$key) && ($this->$key instanceof \SwaggerValidator\Object\ParameterBody)) {
                $check = $check && $this->checkExistsEmptyValidate($context->setDataPath(\SwaggerValidator\Common\FactorySwagger::LOCATION_BODY)->setLocation(\SwaggerValidator\Common\FactorySwagger::LOCATION_BODY)->setSandBox(), $key);
            }
            elseif (is_object($this->$key) && ($this->$key instanceof \SwaggerValidator\DataType\TypeCommon)) {
                $check = $check && $this->checkExistsEmptyValidate($context->setDataPath($this->$key->name)->setLocation($this->$key->in)->setSandBox(), $key);
            }
        }

        $context->logValidate(get_class($this), __METHOD__, __LINE__);
        return true;
    }

    protected function checkExistsEmptyValidate(\SwaggerValidator\Common\Context $context, $key)
    {
        $context->dataLoad();
        $keyRequired = \SwaggerValidator\Common\FactorySwagger::KEY_REQUIRED;

        if ($this->$key->isRequired() == true && !$context->isDataExists()) {
            $context->setValidationError(\SwaggerValidator\Common\Context::VALIDATION_TYPE_NOTFOUND, $context->getDataPath() . ' is not found', __METHOD__, __LINE__);
        }
        elseif (!$context->isDataExists()) {
            return true;
        }

        return $this->$key->validate($context);
    }

    function getModel(\SwaggerValidator\Common\Context $context, $listParameters)
    {
        foreach ($this->keys() as $key) {
            $model = null;
            $in    = null;
            $name  = null;

            if (is_object($this->$key) && ($this->$key instanceof \SwaggerValidator\Object\ParameterBody)) {
                $name  = \SwaggerValidator\Common\FactorySwagger::LOCATION_BODY;
                $in    = \SwaggerValidator\Common\FactorySwagger::LOCATION_BODY;
                $model = $this->$key->getModel($context->setDataPath($key));
            }
            elseif (is_object($this->$key) && ($this->$key instanceof \SwaggerValidator\DataType\TypeCommon)) {
                $name  = $this->$key->name;
                $in    = $this->$key->in;
                $model = $this->$key->getModel($context->setDataPath($key));
            }

            if (!array_key_exists($in, $listParameters) || !is_array($listParameters[$in])) {
                $listParameters[$in] = array();
            }

            $listParameters[$in][$name] = $model;
        }

        $context->logModel(__METHOD__, __LINE__);
        return $listParameters;
    }

}

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
 * Description of Contact
 *
 * @author Nicolas JUHEL<swaggervalidator@nabbar.com>
 * @version 1.0.0
 */
class Contact extends \SwaggerValidator\Common\CollectionSwagger
{

    public function __construct()
    {

    }

    public function jsonUnSerialize(\SwaggerValidator\Common\Context $context, $jsonData)
    {
        $this->checkJsonObject($context, $jsonData);

        foreach (get_object_vars($jsonData) as $key => $value) {
            $this->$key = \SwaggerValidator\Common\FactorySwagger::getInstance()->jsonUnSerialize($context->setDataPath($key), $this->getCleanClass(__CLASS__), $key, $value);
        }

        \SwaggerValidator\Common\Context::logDecode($context->getDataPath(), get_class($this), __METHOD__, __LINE__);
    }

}
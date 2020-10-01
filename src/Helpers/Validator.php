<?php
declare(strict_types=1);

namespace TesteHibridoApp\Helpers;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationException;

/**
 * Validation service
 */
class Validator
{
    /**
     * Validate array of data with given rules
     * @param array $source
     * @param array $rules
     * @throws ValidationException
     */
    public function validateArray(array $values, array $rules, $messages)
    {
        $translator = new Translator(new ArrayLoader(), 'en_US');
        $validatorFactory = new ValidatorFactory($translator);
        $validator = $validatorFactory->make($values, $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}